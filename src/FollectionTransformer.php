<?php

namespace Infira\Follection;


use ArrayAccess;
use Illuminate\Support\Str;
use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\Exceptions\NotImplementedException;
use Infira\Follection\Storage\CollectionGateway;
use RuntimeException;
use Wolo\AttributesBag\AttributesBagManager;
use Wolo\AttributesBag\HasAttributes;
use Wolo\Globals\Globals;
use Wolo\Globals\GlobalsCollection;

/**
 * @template TKey of array-key
 * @template TValue
 */
abstract class FollectionTransformer extends CollectionGateway implements
    HasAttributes
    , ArrayAccess
    , FollectionItem
{
    protected static array $cache = [];
    private string $id;
    use Traits\FollectionItemProxies;
    use Traits\FollectionCasting;
    use Traits\FollectionIterator;
    use Traits\FollectionParent;
    use AttributesBagManager;

    protected mixed $overloadDefaultValue = null;

    /**
     * @var class-string must implement FollectionItem
     */
    protected string $itemTransformerClass;

    public function __construct(
        mixed $data = []
    ) {
        if (!isset($this->itemTransformerClass)) {
            throw new RuntimeException('item transformer class it not defined');
        }
        if (!class_exists($this->itemTransformerClass)) {
            throw new RuntimeException(("item transformer class('$this->itemTransformerClass') does not exists"));
        }
        $this->id = uniqid('', true);
        self::$cache[$this->id] = &$this;
        parent::__construct($data);
        $this->init();
    }

    public static function fromCache(string $id): static
    {
        return self::$cache[$id];
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function init(): void {}

    public function __call(string $method, array $arguments): mixed
    {
        #TODO Collection has macros
        throw new NotImplementedException("FollectionTransformer method('$method') not implemented");
    }

    public function __set(string $name, $value): void
    {
        $this->storage->put($name, $value);
    }

    public function __isset(string $name): bool
    {
        return $this->storage->has($name);
    }

    public function getTransformedValue($key, mixed $default = null): mixed
    {
        if ($this->hasProxy($key)) {
            return $this->getProxy($key);
        }

        //$this->debugValue($key, 'realStorageValue', $this->storage->get($key, 'NOT SET'));

        $progress = $this->tmp('getTransformedValue.progress');
        if ($progress->has($key)) {
            throw new RuntimeException("key('$key') transformation is not finished, transforming unfinished will cause infinite loop");
        }
        $progress->put($key, true);


        if ($this->storage->has($key)) {
            $rawValue = $this->storage->get($key);
        }
        else if ($this->hasDefaultValueMethod($key)) {
            $rawValue = $this->getDefaultValueMethodValue($key);
        }
        else {
            //$this->debugValue($key, '$default', $default);
            $rawValue = value($default, $key);
        }

        $value = $this->transformItem($rawValue, $key);
//        if ((string)$key === 'calasdasdculatedFinalPrice') {
//            debug([
//                'trace' => getTrace(),
//                '$this' => $this::class,
//                'clean' => $this->toArray(),
//                '$value('.'<span style="color:purple">'.$key.'</span>'.')' => [
//                    '$key' => $key,
//                    ...$this->tmp('debugValue')->of($key)->all()
//                ],
//            ]);
//        }

        $progress->forget($key);

        return $value;
    }

    public function transformItem(mixed $value, int|string $key = null, string $transformClass = null): mixed
    {
        if ($key === null) {
            throw new RuntimeException('cant save item to storage');
        }

        if ($value instanceof FollectionItem) {
            if (!$value->hasParentId()) {
                return $value->setParentId($this->getId());
            }

            return $value;
        }
        //$this->debugValue($key, 'beforeTransform', $value);

        if ($this->canCast($key)) {
            //$this->debugValue($key, 'beforeCast', $value);
            $value = $this->cast($key, $value);
            //$this->debugValue($key, 'afterCast', $value);
        }

        if ($this->hasAccessor($key)) {
            $value = $this->getAccessorValue($key, $value);
        }
        return $this->transformValue($value, $transformClass);
    }

    public function transformValue(mixed $value, string $transformClass = null)
    {
        $class = $transformClass ?: $this->itemTransformerClass;
        $transformed = ($value instanceof FollectionItem) ? $value : new $class($value);

        if ($transformed instanceof FollectionItem) {
            return $transformed->setParentId($this->getId());
        }

        return $transformed;
    }

    protected function hasDefaultValueMethod(string $key): bool
    {
        return method_exists($this, 'default'.Str::studly($key));
    }

    protected function getDefaultValueMethodValue(string $key): mixed
    {
        return $this->{'default'.Str::studly($key)}();
    }

    /**
     * Create a new instance if the value isn't one already.
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TMakeKey, TMakeValue>|iterable<TMakeKey, TMakeValue>|null $items
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make(mixed $items = []): static
    {
        return new static($items);
    }

    /**
     * Create a new instance nad passes attributes from self
     *
     * @param mixed $data
     * @return $this
     */
    public function new(mixed $data = []): static
    {
        if ($data instanceof self) {
            $result = static::make($data->all());
        }
        else {
            $result = static::make($data);
        }
        $result->copyAttributes($this);

        return $result;
    }

    protected function copyAttributes(self $from): void
    {
        $this->setAttributes($from->getAttributes());
    }

    /**
     * Execute $callback once by hash-sum of $parameters
     *
     * @param mixed ...$keys - will be used to generate hash sum ID for storing $callback result <br>
     * If $keys contains only callback then hash sum will be generated Closure signature
     * @param callable $callback method result will be set to memory for later use
     * @return mixed - $callback result
     * @noinspection PhpDocSignatureInspection
     * @see          Hash::hashable()
     */
    public function once(mixed ...$param): mixed
    {
        return Globals::once(static::class, $this->id, ...$param);
    }

    public function getUnderlyingValue(): array
    {
        return $this->toArray();
    }

    //region ArrayAccess

    public function offsetExists($offset): bool
    {
        return $this->storage->has($offset);
    }

    //endregion

    private function debugValue($key, string $name, $value): mixed
    {
        if (func_num_args() === 1) {
            return $this->tmp('debugValue')->of($key)->get($name);
        }
        $this->tmp('debugValue')->of($key)->put(self::getTypeString($name, $value), $value);

        return null;
    }

    private static function getTypeString(string $name, mixed $value): string
    {
        if (is_object($value) || is_array($value)) {
            $type = '';
        }
        else {
            $type = ' (type:<span style="color:purple;font-weight: bold">'.get_debug_type($value).'</span>)';
        }

        return '<span style="color:red">'.$name.'</span>'.$type;
    }

    private function useCallable(callable|array $callable, array $arguments = []): mixed
    {
        if (is_callable($callable)) {
            return $callable(...$arguments);
        }

        if (is_array($callable)) {
            if (is_string($callable[0]) && is_string($callable[1])) {
                [$object, $method] = $callable;
            }
            else {
                [$object, $method] = $callable[0];
                array_push($arguments, ...$callable[1]);
            }
            if ($this instanceof $object) {
                return $this->$method(...$arguments);
            }

            return $object->$method(...$arguments);
        }

        throw new RuntimeException('unknonwn proxy');
    }

    private function tmp(string $name): GlobalsCollection
    {
        return Globals::of(__CLASS__.__FUNCTION__.$this->id)->of($name);
    }
}
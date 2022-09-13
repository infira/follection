<?php

namespace Infira\Follection\Storage;

use Illuminate\Contracts\Support\Arrayable;
use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\FollectionTransformer;
use Infira\Follection\Support\Item;
use stdClass;
use Wolo\Contracts\UnderlyingValueByKey;

/**
 * @template TKey of array-key
 * @template TValue
 */
class KeyValueItem extends ValueRetriever implements Arrayable
{
    public function __construct(mixed $value, private readonly int|string $key)
    {
        if ($value instanceof self) {
            throw new \RuntimeException('cant have value of self');
        }
        parent::__construct($value);
    }

    public function value(): mixed
    {
        return $this->target;
    }

    public function key(): int|string
    {
        return $this->key;
    }

    public function transformItem(FollectionTransformer|CollectionGateway $follection): mixed
    {
        return $follection->transformItem($this->target, $this->key);
    }

    public function toArray(): array
    {
        if (is_array($this->target)) {
            return $this->target;
        }
        if ($this->target instanceof Arrayable) {
            return $this->target->toArray();
        }
        if ($this->target instanceof stdClass) {
            return (array)$this->target;
        }
        throw new \RuntimeException('cant convert to toArray');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->validateFluentValue();
        if ($this->target instanceof FollectionItem) {
            $this->target->put($offset, $value);

            return;
        }

        if ($this->target instanceof \ArrayAccess) {
            $this->target->offsetGet($offset, $value);

            return;
        }

        if (is_array($this->target)) {
            $this->target[$offset] = $value;

            return;
        }

        throw new \RuntimeException(__CLASS__."  offsetSet is not possible");
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!$this->offsetExists($offset)) {
            throw new \RuntimeException(__CLASS__." does not has offset('$offset')");
        }
        $this->validateFluentValue();
        if ($this->target instanceof FollectionItem) {
            $this->target->forget($offset);

            return;
        }

        if ($this->target instanceof \ArrayAccess) {
            $this->target->offsetUnset($offset);

            return;
        }

        if (is_array($this->target)) {
            unset($this->target[$offset]);

            return;
        }

        throw new \RuntimeException(__CLASS__."  offsetSet is not possible");
    }

    public function itemValueGet($key): mixed
    {
        if ($this->useAsCallable($key)) {
            return $key($this->target, $this->key);
        }

        $this->validateFluentValue();
        if (!$this->valueExists($key)) {
            throw new \RuntimeException(__CLASS__." does not has offset('$key')");
        }

        if ($this->target instanceof UnderlyingValueByKey) {
            return Item::underlyingValue($this->target->valueAt($key));
        }

        return Item::clean(data_get($this->target, $key));
    }

    protected function useAsCallable($value): bool
    {
        return !is_string($value) && is_callable($value);
    }
}
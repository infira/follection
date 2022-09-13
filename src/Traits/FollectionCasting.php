<?php
/** @noinspection PhpUnused */

namespace Infira\Follection\Traits;


use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Infira\Follection\Casts\Attribute;
use Infira\Follection\Casts\Cast;
use Infira\Follection\FollectionTransformer;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

/**
 * @mixin FollectionTransformer
 * @template TKey
 * @template TValue
 */
trait FollectionCasting
{
    /**
     * @var array<TKey,(callable(TValue): mixed)|string|object>
     */
    protected array $casts = [];
    protected static array $attributeMutatorCache = [];
    protected static array $getAttributeMutatorCache = [];
    private array $attributeCastCache = [];

    /**
     * Cast item values by $keys
     * Will be used just before transforming value ot setting new value for storage
     *
     * @param  string|int|array  $key
     * @param  callable|string  $cast
     * Supported types casts int|integer 'bool|boolean', 'float|double|real', 'string' ,'classString' ,'object', 'unset'
     * classString = MyClass::class
     * See for more details https://www.php.net/manual/en/language.types.type-juggling.php
     *
     * @return $this
     */
    public function putCasting(string|int|array $key, callable|string $cast): static
    {
        foreach ((array)$key as $k) {
            $this->casts[$k] = $cast;
        }

        return $this;
    }

    /**
     * Forget item value casting by item key
     *
     * @param  string|int  ...$key
     * @return $this
     */
    public function forgetCasting(string|int ...$key): static
    {
        foreach ($key as $f) {
            if ($this->hasCasting($f)) {
                unset($this->casts[$f]);
            }
        }

        return $this;
    }

    public function hasCasting(string|int $key): bool
    {
        return array_key_exists($key, $this->casts);
    }

    public function canCast(string|int $key): bool
    {
        if (!$this->hasCasting($key)) {
            return false;
        }
        $cast = $this->casts[$key];
        if ($cast instanceof Cast) {
            return $cast->isCasted;
        }

        return true;
    }

    private function cast(string|int $key, mixed $value): mixed
    {
        $cast = $this->getCast($key);
        $cast->isCasted = true;
        $this->casts[$key] = $cast;

        return match ($this->getCastType($cast)) {
            'callable' => $this->useCallable($cast->format, [$value, $key]),
            'int', 'integer' => (int)$value,
            'real', 'float', 'double' => (float)$value,
            'decimal' => number_format(
                (float)$value,
                abs((int)$cast->format),
                '.',
                ''
            ),
            'string' => (string)$value,
            'bool', 'boolean' => (bool)$value,
            'array' => (array)$value,
            'date' => $this->asDateTime($value)->startOfDay(),
            'datetime' => $this->asDateTime($value),
            'custom_date' => $this->asDateTime($value)->format($cast->format),
            'timestamp' => $this->asDateTime($value)->getTimestamp()
        };
    }

    private function getCastType(Cast $cast): string
    {
        if (str_starts_with($cast->cast, 'decimal:')) {
            return 'decimal';
        }

        if (str_starts_with($cast->cast, 'date:') || str_starts_with($cast->cast, 'datetime:')) {
            return 'custom_date';
        }

        return $cast->cast;
    }

    private function getCast(string $key): Cast
    {
        $cast = $this->casts[$key];
        if ($cast instanceof Cast) {
            return $cast;
        }
        if (is_string($cast)) {
            $ex = explode(':', $cast, 2);
            if (!isset($ex[1])) {
                $ex[1] = null;
            }
            [$cast, $format] = $ex;

            return new Cast($cast, $format);
        }

        if (is_callable($cast)) {
            return new Cast('callable', $cast);
        }

        if (is_array($cast)) {
            if (isset($cast['cast'])) {
                return new Cast($cast['cast'], $cast['format'] ?? null);
            }

            return new Cast('callable', $cast);
        }
        throw new RuntimeException('unkmown cast');
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    private function asDateTime(mixed $value): \Carbon\Carbon
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof CarbonInterface) {
            return Date::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'),
                $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        if ($this->isStandardDateFormat($value)) {
            return Date::instance(Carbon::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        try {
            $date = Date::createFromFormat('Y-m-d', $value);
        }
        catch (InvalidArgumentException $e) {
            $date = false;
        }

        return $date ?: Date::parse($value);
    }

    private function isStandardDateFormat(string $value): bool
    {
        return (bool)preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    //region accessorss and mutators
    public function hasAttributeMutator($key)
    {
        if (isset(static::$attributeMutatorCache[get_class($this)][$key])) {
            return static::$attributeMutatorCache[get_class($this)][$key];
        }

        if (!method_exists($this, $method = Str::camel($key))) {
            return static::$attributeMutatorCache[get_class($this)][$key] = false;
        }

        $returnType = (new ReflectionMethod($this, $method))->getReturnType();

        //debug([$key => $returnType->getName()]);

        return static::$attributeMutatorCache[get_class($this)][$key] = ($returnType instanceof ReflectionNamedType &&
            $returnType->getName() === Attribute::class);
    }

    public function hasAccessor(string $key): bool
    {
        if (isset(static::$getAttributeMutatorCache[get_class($this)][$key])) {
            return static::$getAttributeMutatorCache[get_class($this)][$key];
        }

        if (!$this->hasAttributeMutator($key)) {
            return static::$getAttributeMutatorCache[get_class($this)][$key] = false;
        }

        return static::$getAttributeMutatorCache[get_class($this)][$key] = is_callable($this->{Str::camel($key)}()->get);
    }

    public function getAccessorValue(string $key, mixed $value): mixed
    {
        if (isset($this->attributeCastCache[$key])) {
            return $this->attributeCastCache[$key];
        }

        $attribute = $this->{Str::camel($key)}();

        return $this->attributeCastCache[$key] = ($attribute->get)($value, $key);
    }
    //endregion
}
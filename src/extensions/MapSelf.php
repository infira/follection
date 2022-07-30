<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait MapSelf
{
    /**
     * Map values into static::class and uses callback
     *
     * @template TMapValue
     *
     * @param callable(static::class): TMapValue $callback
     * @return static<TKey, TValue>
     * @see \Illuminate\Support\Collection::map()
     * @see \Illuminate\Support\Collection::mapInto()
     */
    public function mapSelf(callable $callback = null)
    {
        return $this->map(fn($value, $key) => $callback(new static($value, $key)));
    }
}
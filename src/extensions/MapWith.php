<?php

namespace Infira\Collection\extensions;


/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait MapWith
{
    /**
     * Map values into class with callback
     * if $callback is null then regular mapInto is called
     * ex: $callback(new $class(TValue, TKey)
     *
     * @template TMapIntoValue
     * @template TClassValue - class-string<TValue, TKey>
     * @template TMapValue
     *
     * @param class-string<TValue, TKey> $class
     * @param callable(TClassValue): TMapValue $callback
     *
     * @return static<TKey, TMapValue>
     * @see \Illuminate\Support\Collection::map()
     * @see \Illuminate\Support\Collection::mapInto()
     */
    public function mapWith(string $class, callable $callback = null)
    {
        if (!$callback) {
            return $this->mapInto($class);
        }

        return $this->map(fn($value, $key) => $callback(new $class($value, $key)));
    }
}
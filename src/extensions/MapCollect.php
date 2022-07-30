<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait MapCollect
{
    /**
     * Map values into Collection with optional callback
     * if $callback is null then regular mapInto(\Illuminate\Support\Collection) is called
     *
     * @template TMapIntoValue
     * @template TClassValue - class-string<TValue, TKey>
     * @template TMapValue
     *
     * @param callable(TClassValue): TMapValue $callback
     * @return static<TKey, TMapValue>
     * @see \Illuminate\Support\Collection::map()
     * @see \Illuminate\Support\Collection::mapInto()
     */
    public function mapCollect(callable $callback = null)
    {
        if (!$callback) {
            return $this->mapInto(\Illuminate\Support\Collection::class);
        }

        return $this->map(fn($value, $key) => $callback(new \Illuminate\Support\Collection($value, $key)));
    }
}
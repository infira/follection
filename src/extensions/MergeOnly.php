<?php

namespace Infira\Collection\extensions;


/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait MergeOnly
{
    /**
     * Merge the collection with the given items and only keys present in $items, or provide $keys with parameter
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
     * @return static
     * @see \Illuminate\Support\Collection::only()
     * @see \Illuminate\Support\Collection::merge()
     */
    public function mergeOnly($items, array $keys = null)
    {
        $items = $this->getArrayableItems($items);
        if ($keys === null) {
            $keys = array_keys($items);
        }
        else {
            if ($keys instanceof \Illuminate\Support\Enumerable) {
                $keys = $keys->all();
            }
            $keys = is_array($keys) ? $keys : array_slice(func_get_args(), 1);
        }

        return new static(array_merge(array_flip($keys), \Illuminate\Support\Arr::only($items, $keys)));
    }
}
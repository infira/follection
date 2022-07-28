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
     * Merge the collection with the given items and only keys present in $items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function mergeOnly($items)
    {
        $items = $this->getArrayableItems($items);
        $keys = array_keys($items);

        return new static(array_merge(array_flip($keys), \Illuminate\Support\Arr::only($items, $keys)));
    }
}
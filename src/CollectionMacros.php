<?php

namespace Infira\Collection;

/**
 * @mixin \Illuminate\Support\Collection
 */
class CollectionMacros
{
    public static function mapIntoCollection(): \Closure
    {
        return function () {
            return $this->mapInto(\Illuminate\Support\Collection::class);
        };
    }


    public static function mapOnly(): \Closure
    {
        return function ($keys) {
            return $this->map(fn($item) => collect($item)->only($keys)->all());
        };
    }


    public static function mergeOnly(): \Closure
    {
        return function ($items) {
            $items = $this->getArrayableItems($items);
            $keys = array_keys($items);

            return new static(array_merge(array_flip($keys), \Illuminate\Support\Arr::only($items, $keys)));
        };
    }
}

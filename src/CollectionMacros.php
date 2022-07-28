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
}

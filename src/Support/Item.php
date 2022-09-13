<?php

namespace Infira\Follection\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Stringable;
use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\FollectionTransformer;
use stdClass;
use Traversable;
use Wolo\Contracts\UnderlyingValue;

/**
 * @internal
 */
class Item
{
    public static function underlyingValue(mixed $target): mixed
    {
        if ($target instanceof FollectionItem) {
            return $target->getUnderlyingValue();
        }

        if ($target instanceof UnderlyingValue) {
            return self::underlyingValue($target->value());
        }

        if ($target instanceof Stringable) {
            return $target->toString();
        }
        if ($target instanceof Enumerable) {
            return $target->toArray();
        }

        return $target;
    }

    public static function exists($array, $key): bool
    {
        if ($array instanceof stdClass) {
            $array = (array)$array;
        }

        return Arr::exists($array, $key);
    }

    public static function clean(mixed $target): mixed
    {
        $target = self::underlyingValue($target);
        if ($target instanceof Traversable) {
            $target = iterator_to_array($target);
        }
        if (is_array($target)) {
            foreach ($target as $k => $v) {
                $target[$k] = self::clean($v);
            }

            return $target;
        }

        return $target;
    }

    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            }
            elseif ($values instanceof FollectionTransformer) {
                $values = $values->all();
            }
            elseif (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    public static function flatten(array $array, $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            $item = $item instanceof FollectionTransformer ? $item->all() : $item;
            $item = $item instanceof Collection ? $item->all() : $item;

            if (!is_array($item)) {
                $result[] = $item;
            }
            else {
                $values = $depth === 1
                    ? array_values($item)
                    : self::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}
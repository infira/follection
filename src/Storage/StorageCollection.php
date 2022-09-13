<?php

namespace Infira\Follection\Storage;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @template TKey of array-key
 * @template TValue
 */
class StorageCollection extends Collection
{
    public function hasHighOrderProxy(string $name): bool
    {
        return in_array($name, static::$proxies);
    }

    public function lazy(): LazyCollection
    {
        return new LazyCollection($this->items);
    }

    /**
     * Create a new collection.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
     * @return static
     */
    public function exchange($items = []): static
    {
        $this->items = $this->getArrayableItems($items);

        return $this;
    }

    /* @inheritDoc */
    protected function valueRetriever($value): callable
    {
        if ($this->useAsCallable($value)) {
            return parent::valueRetriever($value);
        }

        return static function ($item) use ($value) {
            if ($item instanceof ValueRetriever) {
                $retrieved = $item;
            }
            else {
                $retrieved = (new ValueRetriever($item));//->valueGet($key);
            }

//            debug([
//                'valueRetriever: <span style="color:red">'.$value.'</span>' => [
//                    '$item' => $item,
//                    //'result' => data_get($item, $value)
//                    '$retrieved' => $retrieved->valueGet($value)
//                ]
//            ]);

            return $retrieved->valueGet($value);
        };
    }

    /* @inheritDoc */
    protected function operatorForWhere($key, $operator = null, $value = null): \Closure
    {
        if ($this->useAsCallable($value)) {
            return parent::valueRetriever($value);
        }

        $args = func_get_args();

        return function ($item) use ($key, $args) {
            $comparor = parent::operatorForWhere(...$args);
            if ($item instanceof ValueRetriever) {
                $retrieved = $item;
            }
            else {
                $retrieved = (new ValueRetriever($item));//->valueGet($key);
            }
//            debug([
//                'operatorForWhere: <span style="color:red">'.$key.'</span>' => [
//                    //'$item' => $item,
//                    '$retrieved' => $retrieved,
//                    'data_get' => data_get($retrieved, $key)
//                ]
//            ]);

            return $comparor($retrieved);
        };
    }
}
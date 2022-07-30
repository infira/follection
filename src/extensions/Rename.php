<?php

namespace Infira\Collection\extensions;


/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait Rename
{
    /**
     * Rename array keys with new one while maintains array order
     *
     * @param TKey|array<TKey => TKey> $from
     * @param TKey $to = null
     *
     * @return static
     * @example rename('from-key','to-key')
     * @example rename(['from-key-1'=>'to-key-1', 'from-key-2'=>'to-key-2'])
     *
     */
    public function rename($from, $to = null)
    {
        $from = (is_string($from) and is_string($to)) ? [$from, $to] : $this->getArrayableItems($from);

        $this->items = array_combine(
            array_map(static fn($key) => $from[$key] ?? $key, array_keys($this->items)),
            $this->items);

        return $this;
    }
}
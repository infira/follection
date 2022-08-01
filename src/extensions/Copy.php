<?php

namespace Infira\Collection\extensions;


/**
 * @mixin \Illuminate\Support\Collection
 */
trait Copy
{
    /**
     * Rename array keys with new one while maintains array order
     * @template TGetDefault
     * @template TTKey of array-key - copy to key
     * @template TFKey of array-key - copy from key
     *
     * @param TFKey|array<TFKey,TTKey> $fromKey
     * @param TTKey|null $toKey
     * @param TGetDefault|(\Closure(): TGetDefault) $default
     *
     * @return static
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/copy.md
     *
     */
    public function copy(string|int|array $fromKey, string|int|null $toKey = null, mixed $default = null)
    {
        if ($fromKey && $toKey !== null) {
            return $this->put($toKey, $this->get($fromKey, $default));
        }
        foreach ($fromKey as $fk => $tks) {
            foreach ((array)$tks as $tk) {
                $this->put($tk, $this->get($fk, $default));
            }
        }

        return $this;
    }
}
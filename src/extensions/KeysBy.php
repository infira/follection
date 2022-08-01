<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait KeysBy
{
    /**
     * Map values into Collection with optional callback
     * if is_callable($keys) then regular \Illuminate\Support\Collection->keyBy()
     *
     * @param array<TKey>|string|callable $keys
     * @return static
     * @see \Illuminate\Support\Collection::keyBy()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/keysBy.md
     */
    public function keysBy($keys, string $glue = '.')
    {
        if (is_callable($keys)) {
            return $this->keyBy($keys);
        }
        if (is_string($keys)) {
            $keys = array_map('trim', explode('.', $keys));
        }

        return $this->keyBy(function ($item) use ($keys, $glue) {
            return collect($item)->only($keys)->join($glue);
        });
    }
}
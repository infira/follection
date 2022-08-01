<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait ForgetBy
{
    /**
     * Remove an item from the collection by key or using callback
     *
     * @param TKey|array<array-key, TKey>|callable(TValue, TKey): bool $keys
     * @return static
     * @see \Illuminate\Support\Collection::forget()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/forgetBy.md
     */
    public function forgetBy($keys): static
    {
        if (is_string($keys) || is_array($keys)) {
            return $this->forget($keys);
        }
        foreach ($this as $key => $value) {
            if ($keys($value, $key)) {
                $this->forget($key);
            }
        }

        return $this;
    }
}
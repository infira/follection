<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait MapOnly
{
    /**
     * Get the items with the specified keys for each member current collection item
     *
     * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
     * @return static
     * @see \Illuminate\Support\Collection::only()
     */
    public function mapOnly($keys): static
    {
        return $this->map(fn($item) => collect($item)->only($keys)->all());
    }
}
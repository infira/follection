<?php

/**
 * This is IDE helper for suggest/autocomplete
 * @noinspection all
 */

namespace Illuminate\Support;

class Collection
{
    /**
     * Map each collection item to own collection
     * @return static
     * @see \Illuminate\Support\Collection::mapInto()
     */
    public function mapIntoCollection(): static
    {
        /** @see \Infira\Collection\extensions\MapIntoCollection::mapIntoCollection() */;
    }


    /**
     * Get the items with the specified keys for each member current collection item
     *
     * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
     * @return static
     * @see \Illuminate\Support\Collection::only()
     */
    public function mapOnly($keys): static
    {
        /** @see \Infira\Collection\extensions\MapOnly::mapOnly() */;
    }
}

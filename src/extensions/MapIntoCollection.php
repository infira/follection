<?php

namespace Infira\Collection\extensions;

/**
 * @mixin \Illuminate\Support\Collection
 */
trait MapIntoCollection
{
    /**
     * Map each collection item to own collection
     * @return static
     * @see \Illuminate\Support\Collection::mapInto()
     */
    public function mapIntoCollection(): static
    {
        return $this->mapInto(\Illuminate\Support\Collection::class);
    }
}
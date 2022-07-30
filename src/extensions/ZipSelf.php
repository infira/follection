<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait ZipSelf
{
    /**
     * zips collection using own keys and values
     *
     * @template TZipValue
     *
     * @return static<int, static<int, TValue|TZipValue>>
     */
    public function zipSelf()
    {
        return $this->keys()->zip($this->values());
    }
}
<?php

namespace Infira\Collection\extensions;

/**
 * @mixin \Illuminate\Support\Collection
 */
trait PipeIntoSelf
{
    /**
     * PipeInto using static::class
     *
     * @return static
     * @see \Illuminate\Support\Collection::pipeInto()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/pipeIntoSelf.md
     */
    public function pipeIntoSelf(): static
    {
        return $this->pipeInto(static::class);
    }
}
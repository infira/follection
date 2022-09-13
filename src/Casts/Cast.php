<?php

namespace Infira\Follection\Casts;

/**
 * Inspirted by laravel eloquent mutators
 *
 * @see https://laravel.com/docs/9.x/eloquent-mutators#attribute-casting
 */
class Cast
{
    public bool $isCasted = false;

    /**
     * @param  string|class-string  $cast
     * @param  string|class-string|callable|null  $format
     */
    public function __construct(
        public readonly string $cast,
        public readonly mixed $format = null
    ) {}
}

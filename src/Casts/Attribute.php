<?php

namespace Infira\Follection\Casts;

/**
 * Inspirted by laravel eloquent mutators
 *
 * @see https://laravel.com/docs/9.x/eloquent-mutators#attribute-casting
 */
class Attribute
{
    /**
     * The attribute accessor.
     *
     * @var callable
     */
    public $get;

    /**
     * The attribute mutator.
     *
     * @var callable
     */
    public $set;

    public function __construct(callable $get = null, callable $set = null)
    {
        $this->get = $get;
        $this->set = $set;
    }

    public static function get(callable $get): static
    {
        return new static($get);
    }

    public static function set(callable $set): static
    {
        return new static(null, $set);
    }
}

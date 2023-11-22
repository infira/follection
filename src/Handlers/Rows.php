<?php

namespace Infira\Follection\Handlers;


use Infira\Follection\FollectionTransformer;
use Infira\Follection\Iterators\RowIterator;
use IteratorAggregate;

/**
 * @method Record|null first(callable $callback = null, $default = null)
 * @method Record|null last(callable $callback = null, $default = null)
 * @method Record get($key, $default = null)
 * @method Record offsetGet(mixed $key)
 */
class Rows extends FollectionTransformer implements IteratorAggregate
{
    protected string $itemTransformerClass = Record::class;

    public function getIterator(): RowIterator
    {
        return new RowIterator($this);
    }
}
<?php

namespace Infira\Follection\Iterators;


use ArrayIterator;
use Infira\Follection\FollectionTransformer;

class FollectionIterator extends ArrayIterator
{
    private string|int|null $firstKey;
    private string|int|null $lastKey;


    public function __construct(private readonly FollectionTransformer $list)
    {
        $data = $list->all();
        $this->firstKey = array_key_first($data);
        $this->lastKey = array_key_last($data);
        parent::__construct($data);
    }

    public function current(): mixed
    {
        $key = $this->key();
        $item = $this->list->transformItem(parent::current(), $key);
        if ($item instanceof FollectionTransformer) {
            if ($key === $this->firstKey) {
                $item->setIsFirst();
            }
            else if ($key === $this->lastKey) {
                $item->setIsLast();
            }
        }

        return $item;
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->list->get($key, null);
    }
}
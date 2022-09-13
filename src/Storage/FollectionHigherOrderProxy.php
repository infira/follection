<?php

namespace Infira\Follection\Storage;

use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\FollectionTransformer;

class FollectionHigherOrderProxy
{
    protected string $key;

    public function __construct(protected FollectionTransformer $follection, protected string $method) {}

    public function __get(string $key)
    {
        $this->key = $key;

        return $this;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->follection->{$this->method}(function (FollectionItem $item) use ($method, $parameters) {
            if (isset($this->key)) {
                $item = $item->get($this->key);
            }
            return $item->{$method}(...$parameters);
        });
    }
}

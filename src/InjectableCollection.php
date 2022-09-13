<?php

namespace Infira\Collection;

use Illuminate\Support\Collection;
use Infira\Collection\helpers\InjectableHelper;

/**
 * @mixin Collection
 */
class InjectableCollection
{
    public function __construct(protected Collection $collection) {}

    public function __call(string $method, array $params)
    {
        return $this->collection->$method(...InjectableHelper::makeIfArray($params));
    }
}

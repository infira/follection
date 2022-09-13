<?php

namespace Infira\Follection\Handlers;


use Infira\FluentValue\FluentValue;
use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\Traits\FollectionParent;

class Field extends FluentValue implements FollectionItem
{
    use FollectionParent;

    public function valueAt($key, $default = null)
    {
        if (!$this->offsetExists($key)) {
            return $default;
        }

        return $this->offsetGet($key);
    }

    public function getUnderlyingValue(): ?string
    {
        return $this->value();
    }
}

<?php

namespace Infira\Follection\Traits;

use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\FollectionTransformer;

trait FollectionParent
{
    private string $parentId;

    public function setParentId(string $id): static
    {
        $this->parentId = $id;

        return $this;
    }

    public function getParent(): FollectionItem
    {
        return FollectionTransformer::fromCache($this->parentId);
    }

    public function hasParentId(): bool
    {
        return isset($this->parentId);
    }

    public function getParentId(): ?string
    {
        return $this->parentId ?? null;
    }
}
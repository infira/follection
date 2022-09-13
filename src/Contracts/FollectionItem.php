<?php

namespace Infira\Follection\Contracts;

use Wolo\Contracts\{UnderlyingValueByKey, UnderlyingValueStatus};

interface FollectionItem extends UnderlyingValueStatus, UnderlyingValueByKey, \JsonSerializable
{
    public function getParent(): FollectionItem;

    public function setParentId(string $id): static;

    public function hasParentId(): bool;

    public function getParentId(): ?string;

    public function getUnderlyingValue(): mixed;
}
<?php

namespace Infira\Follection\Contracts;

interface ValueRetriever
{
    public function valueExists(string|int $key): bool;

    public function valueGet(string|int $key): mixed;
}
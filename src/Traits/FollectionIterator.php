<?php
/** @noinspection PhpUnused */

namespace Infira\Follection\Traits;


use Infira\Follection\FollectionTransformer;

/**
 * @mixin FollectionTransformer
 */
trait FollectionIterator
{
    private bool $isFirst = false;
    private bool $isLast = false;
    private string|int $currentIteratorKey;

    public function setCurrentIteratorKey(int|string $currentIteratorKey): void
    {
        $this->currentIteratorKey = $currentIteratorKey;
    }

    public function setIsFirst(): void
    {
        $this->isFirst = true;
    }

    public function setIsLast(): void
    {
        $this->isLast = true;
    }

    public function isFirst(): bool
    {
        return $this->isFirst;
    }

    public function isLast(): bool
    {
        return $this->isLast;
    }
}
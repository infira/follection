<?php

namespace Infira\Follection\Storage;

use Infira\FluentValue\FluentValue;
use Infira\Follection\Support\Item;
use Wolo\Contracts\UnderlyingValueByKey;

/**
 * @template TKey of array-key
 * @template TValue
 */
class ValueRetriever implements \ArrayAccess, \Infira\Follection\Contracts\ValueRetriever
{
    private string $follectionId;

    public function __construct(protected mixed $target)
    {
        if ($this->target instanceof self) {
            throw new \RuntimeException('cant have value of self');
        }
    }

    public function valueExists(int|string $key): bool
    {
        $this->validateFluentValue();

        return Item::exists($this->target, $key);
    }

    public function valueGet(int|string $key): mixed
    {
        $this->validateFluentValue();
        if (!$this->valueExists($key)) {
            throw new \RuntimeException(__CLASS__." does not has offset('$key')");
        }

        if ($this->target instanceof UnderlyingValueByKey) {
            return Item::underlyingValue($this->target->valueAt($key));
        }

        return Item::clean(data_get($this->target, $key));
    }

    public function offsetExists($offset): bool
    {
        return $this->valueExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->validateFluentValue();

        return $this->valueGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \RuntimeException("cant use offsetSet on ValueRetriever");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \RuntimeException("cant use offsetUnset on ValueRetriever");
    }

    //TODO kas seda on vaja siia?
    protected function validateFluentValue(): void
    {
        if ($this->target instanceof FluentValue) {
            throw new \RuntimeException(__CLASS__."  FluentValue is not implemented");
        }
    }

}
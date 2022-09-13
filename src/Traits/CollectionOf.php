<?php
/**
 * @noinspection PhpInstanceofIsAlwaysTrueInspection
 * @noinspection PhpUnused
 */

namespace Infira\Follection\Traits;


/**
 * @mixin CollectionExtras
 */
trait CollectionOf
{
    public static function of(mixed $items): static
    {
        return new static($items);
    }

    public function isMutatingMethod(string $method): bool
    {
        return in_array($method, [
            'add',
            'push',
            'forget',
            'append',
            'prepend',
            'push',
            'put',
            'transform',
            'add',
        ], true);
    }

    public function isNonMutatingMethod(string $method): bool
    {
        return in_array($method, [
            'dump',
            'each',
            'when',
            'unless',
            'whenEmpty',
            'whenNotEmpty',
            'unlessEmpty',
            'unlessNotEmpty',
            'tap',
            'escapeWhenCastingToString',

        ], true);
    }

    public function isArrayReturningMethod(string $method): bool
    {
        return in_array($method, [
            'unwrap',
            'reduceSpread',
            'toArray',
            'jsonSerialize',
            'all',
            'mode',

        ], true);
    }

    public function isItemReturningMethod(string $method): bool
    {
        return in_array($method, [
            'first',
            'get',
            'last',
            'pull',
            'sole',
            'firstOrFail',
            'offsetGet',
            'firstWhere',
            'value',

        ], true);
    }
}
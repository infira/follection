<?php

namespace Infira\Collection\extensions;

/**
 * @mixin \Illuminate\Support\Collection
 */
trait Chain
{
    /**
     * Chain methods dynamically
     *
     * @template TMethod - collection method
     * @template TArguments - array
     *
     * @param array<TMethod, TArguments>|array<TMethod, array<TArguments>> $chain
     * @return static
     */
    public function chain(array $chain)
    {
        $carry = $this;
        foreach ($chain as $method => $conditions) {
            $conditions = \Illuminate\Support\Arr::isAssoc($conditions) ? $conditions : [$conditions];
            foreach ($conditions as $parameters) {
                $carry = $carry->$method(...$parameters);
            }
        }

        return $carry;
    }
}
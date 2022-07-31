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
     * @template TArguments - mixed[]
     *
     * @param array<TMethod, TArguments>|array<TMethod, array<TArguments>>|\Illuminate\Contracts\Support\Arrayable<array<TMethod, TArguments>> $chain
     * @return static
     * @example https://github.com/infira/laravel-collection-extensions/tree/main/src/chain.md
     */
    public function chain($chain)
    {
        $carry = $this;

        $parsedChain = [];
        foreach (collect($chain)->toArray() as $ck => $cv) {
            if (is_int($ck)) {
                foreach ($cv as $cvk => $cvv) {
                    $parsedChain[$cvk][] = $cvv;
                }
            }
            else {
                $parsedChain[$ck] = array_merge($parsedChain[$ck] ?? [], $cv);
            }
        }
        foreach ($parsedChain as $method => $conditions) {
            foreach ($conditions as $parameters) {
                $carry = $carry->$method(...$parameters);
            }
        }

        return $carry;
    }
}
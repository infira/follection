<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait Inject
{
    /**
     * Inject $callable value type when iterating collection using $method
     * It works similar to mapInto but instead of doing $collection->mapInto(MyClass)->map(fn(Collection $value) => $value->....())
     * you can do $collection->inject(fn(myClass $value) => $value->....())
     *
     * @template TMapIntoValue
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @param string $method - which collection method to iterate over collection
     * @return static<TKey, TMapValue>
     * @throws \ReflectionException
     */
    public function inject(callable $callback, string $method = 'map')
    {
        return $this->$method(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }

    public function toInjectable(): \Infira\Collection\InjectableCollection
    {
        return $this->pipeInto(\Infira\Collection\InjectableCollection::class);
    }
}
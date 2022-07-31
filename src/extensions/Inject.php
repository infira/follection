<?php

namespace Infira\Collection\extensions;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin \Illuminate\Support\Collection
 */
trait Inject
{
    private function _makeInjectable(callable $callback): callable
    {
        $ref = new \ReflectionFunction($callback);
        $types = array_map(function (\ReflectionParameter $p) {
            return $p->getType()->getName();
        }, $ref->getParameters());

        $cast = static function ($params) use ($types): array {
            array_walk($params, function (&$value, $key) use ($types) {
                $type = $types[$key];
                if (!in_array($type, getPHPBuiltInTypes(), true)) {
                    $value = new $type($value);
                }
            }, $params);

            return $params;
        };

        return fn(...$params) => $callback(...$cast($params));
    }

    private function _makeInjectableIf(mixed $callback): mixed
    {
        if (is_array($callback)) {
            return array_map([$this, '_makeInjectableIf'], $callback);
        }
        if (!is_callable($callback)) {
            return $callback;
        }

        return $this->_makeInjectable($callback);
    }

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
        return $this->$method($this->_makeInjectable($callback));
    }

    public function toInjectable(): \Infira\Collection\InjectableCollection
    {
        return $this->pipeInto(\Infira\Collection\InjectableCollection::class);
    }
}
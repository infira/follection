<?php

namespace Infira\Follection\Storage\Traits;

use Closure;
use Illuminate\Support\Enumerable;
use Infira\Error\Error;
use Infira\Follection\Contracts\FollectionItem;
use Infira\Follection\Storage\CachingIterator;
use Infira\Follection\Storage\CollectionGateway;
use Infira\Follection\Storage\KeyValueItem;
use ReflectionParameter;
use stdClass;
use Wolo\Profiler\Prof;
use Wolo\Reflection\Reflection;
use Wolo\TypeJuggling;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin CollectionGateway
 */
trait CollectionGatewayCallableFactory
{
    /**
     * @param  string|null|(callable(TValue,TKey): mixed)  $value
     */
    protected function kvyValueRetriever(mixed $value): Closure
    {
        $value = $this->makeCallback($value);

        return static fn(KeyValueItem $item) => $item->itemValueGet($value);
    }

    /**
     * @param  string|null|(callable(TValue,TKey): mixed)  $callback
     */
    protected function makeCallback(mixed $callback): mixed
    {
        return Prof::measure('makeItemCallback', function () use ($callback) {
            if (!$this->useAsCallable($callback)) {
                return $callback;
            }

            $argumentTypes = array_map(
                static function (ReflectionParameter $p) {
                    $type = $p->getType();
                    if (!$type) {
                        return null;
                    }
                    if ($type instanceof \ReflectionNamedType) {
                        return $type;
                    }

                    return null;
                },
                Reflection::getParameters($callback)
            );
            if (!array_filter($argumentTypes)) {//no type inection is needed
                return $callback;
            }


            return function (mixed ...$args) use ($argumentTypes, &$callback) {
                foreach ($argumentTypes as $i => $type) {
                    if ($type === null) {
                        continue;
                    }
                    if ($args[$i] === null && $type->allowsNull()) {
                        continue;
                    }
                    $type = $type->getName();
                    if (is_a($type, FollectionItem::class, true)) {
                        $args[$i] = $this->transformItem(
                            $args[$i],
                            $args[$i + 1],
                            $type
                        );
                    }
                    elseif ($type === stdClass::class) {
                        if (!($args[$i] instanceof stdClass)) {
                            if ($args[$i] instanceof Enumerable) {
                                $args[$i] = (object)$args[$i]->toArray();
                            }
                            elseif (is_array($args[$i])) {
                                $args[$i] = (object)$args[$i];
                            }
                            else {
                                throw Error::runtimeException('cant convert to stdClass')->with('$value', $args[$i]);
                            }
                        }
                    }
                    elseif ($type === 'array') {
                        if (!is_array($args[$i])) {
                            if ($args[$i] instanceof Enumerable) {
                                $args[$i] = $args[$i]->toArray();
                            }
                            else {
                                $args[$i] = (array)$args[$i];
                            }
                        }
                    }
                    else {
                        $args[$i] = TypeJuggling::cast($args[$i], $type);
                    }
                }

                return $callback(...$args);
            };
        });
    }

    protected function mapArgumentsItemCallback(array $arguments, int|array $callableIndex = 0): array
    {
        foreach ((array)$callableIndex as $index) {
            if (isset($arguments[$index])) {
                $arguments[$index] = $this->makeCallback($arguments[$index]);
            }
        }

        return $arguments;
    }
}
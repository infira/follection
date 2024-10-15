<?php

namespace Infira\Follection\Storage;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\LazyCollection;
use Infira\Error\Error;
use Infira\Follection\Exceptions\NotImplementedException;
use Infira\Follection\Handlers\FollectionRecord;
use Infira\Follection\Handlers\Record;
use Infira\Follection\Handlers\Rows;
use Infira\Follection\Iterators\FollectionIterator;
use Infira\Follection\Support\Item;
use Traversable;
use Wolo\Contracts\UnderlyingValueByKey;
use Wolo\Contracts\UnderlyingValueStatus;
use Wolo\Hash;
use Wolo\Is;
use Wolo\VarDumper;

/**
 * @template TKey of array-key
 * @template TValue
 * @mixin   \Infira\Follection\FollectionTransformer
 */
abstract class CollectionGateway implements Enumerable, UnderlyingValueByKey, UnderlyingValueStatus
{
    protected StorageCollection $storage;
    use Traits\CollectionGatewayCallableFactory;

    /**
     * Indicates that the object's string representation should be escaped when __toString is invoked.
     *
     * @var bool
     */
    protected $escapeWhenCastingToString = false;

    public function __construct(mixed $data)
    {
        $this->storage = new StorageCollection($data);
    }

    public function __get($key)
    {
        if ($this->storage->hasHighOrderProxy($key)) {
            return $this->highOrderProxy($key);
        }

        return $this->getTransformedValue($key, $this->overloadDefaultValue);
    }

    protected function highOrderProxy(string $method): FollectionHigherOrderProxy
    {
        return new FollectionHigherOrderProxy($this, $method);
    }

    /**
     * Create a collection with the given range.
     *
     * @param int $from
     * @param int $to
     * @return static<int, int>
     */
    public static function range($from, $to)
    {
        throw new NotImplementedException("method('range') is not implemented");
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->storage->all();
    }

    /**
     * Get the average value of a given key.
     *
     * @param (callable(TValue): float|int)|string|null $callback
     * @return float|int|null
     */
    public function avg($callback = null): float|int|null
    {
        return $this->kvt()->avg(
            $this->kvyValueRetriever($callback)
        );
    }

    /**
     * Get the median of a given key.
     *
     * @param string|array<array-key, string>|null $key
     * @return float|int|null
     */
    public function median($key = null): float|int|null
    {
        return $this->storage->median($key);
    }

    /**
     * Get the mode of a given key.
     *
     * @param string|array<array-key, string>|null $key
     * @return array<int, float|int>|null
     */
    public function mode($key = null): ?array
    {
        return $this->storage->mode($key);
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return static<int, mixed>
     */
    public function collapse(): static
    {
        return $this->follection(Item::collapse($this->all()));
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        return $this->storage->contains(...$this->mapArgumentsItemCallback(func_get_args()));
    }

    /**
     * Determine if an item exists, using strict comparison.
     *
     * @param (callable(TValue): bool)|TValue|array-key $key
     * @param TValue|null $value
     * @return bool
     */
    public function containsStrict($key, $value = null): bool
    {
        if (func_num_args() === 2) {
            $key = $this->makeCallback($key);

            return $this->kvt()->contains(
                static fn(KeyValueItem $item) => $item->itemValueGet($key) === $value
            );
        }

        return $this->storage->contains(...$this->mapArgumentsItemCallback(func_get_args()));
    }

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null): bool
    {
        return !$this->contains(...func_get_args());
    }

    /**
     * Cross join with the given lists, returning all possible permutations.
     *
     * @template TCrossJoinKey
     * @template TCrossJoinValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TCrossJoinKey, TCrossJoinValue>|iterable<TCrossJoinKey, TCrossJoinValue> ...$lists
     * @return static<int, array<int, TValue|TCrossJoinValue>>
     */
    public function crossJoin(...$lists): static
    {
        return $this->follection($this->storage->crossJoin(...func_get_args()));
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue> $items
     * @return static
     */
    public function diff($items): static
    {
        return $this->follection($this->storage->diff(...func_get_args()));
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue> $items
     * @param callable(TValue, TValue): int $callback
     * @return static
     */
    public function diffUsing($items, callable $callback): static
    {
        throw new NotImplementedException("method('diffUsing') is not implemented");
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function diffAssoc($items): static
    {
        return $this->follection($this->storage->diffAssoc(...func_get_args()));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function diffAssocUsing($items, callable $callback)
    {
        return $this->follection($this->storage->diffAssocUsing($items, $this->makeCallback($callback)));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function diffKeys($items)
    {
        return $this->follection($this->storage->diffKeys($items));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function diffKeysUsing($items, callable $callback)
    {
        return $this->follection($this->storage->diffKeysUsing($items, $this->makeCallback($callback)));
    }

    /**
     * Retrieve duplicate items from the collection.
     *
     * @param (callable(TValue): bool)|string|null $callback
     * @param bool $strict
     * @return static
     */
    public function duplicates($callback = null, $strict = false)
    {
        return $this->follection(
            $this->kvt()->duplicates(
                $this->kvyValueRetriever($callback),
                $strict
            )
        );
    }

    /**
     * Retrieve duplicate items from the collection using strict comparison.
     *
     * @param (callable(TValue): bool)|string|null $callback
     * @return static
     */
    public function duplicatesStrict($callback = null)
    {
        return $this->follection(
            $this->kvt()->duplicatesStrict(
                $this->kvyValueRetriever($callback),
            )
        );
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey> $keys
     * @return static
     */
    public function except($keys): static
    {
        return $this->follection($this->storage->except(...func_get_args()));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @return static
     */
    public function filter(callable $callback = null): static
    {
        return $this->follection(
            $this->storage->filter(
                $this->makeCallback($callback)
            )
        );
    }

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TFirstDefault|(Closure(): TFirstDefault) $default
     * @return TValue|TFirstDefault
     */
    public function first(callable $callback = null, mixed $default = null): mixed
    {
        return $this->kvt()->first($this->makeCallback($callback), $default)?->transformItem($this);
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param int $depth
     * @return static<int, mixed>
     */
    public function flatten($depth = INF): static
    {
        return $this->follection(Item::flatten($depth));
    }

    /**
     * Flip the items in the collection.
     *
     * @return static<TValue, TKey>
     */
    public function flip()
    {
        return $this->follection($this->storage->flip());
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param TKey|array<array-key, TKey> $keys
     * @return $this
     */
    public function forget($keys)
    {
        $this->storage->forget(...func_get_args());

        return $this;
    }

    /**
     * Get an item from the collection by key.
     *
     * @template TGetDefault
     *
     * @param TKey $key
     * @param TGetDefault|(Closure(): TGetDefault) $default
     * @return TValue|TGetDefault
     */
    public function get($key, mixed $default = null): mixed
    {
        return $this->getTransformedValue($key, $default);
    }

    /**
     * Make checksum
     *
     * @template TGetDefault
     *
     * @param callable|array<TKey>|null $key
     * @param string $algo
     * @return string
     * @link https://www.php.net/manual/en/function.hash-algos.php
     * @link https://www.php.net/manual/en/function.hash.php
     */
    public function checksum(callable|array|null $key = null, string $algo = 'md5'): string
    {
        if (is_null($key)) {
            $from = $this->toArray();
        }
        else if (is_string($key) || is_array($key)) {
            $keys = (array)$key;
            $from = $this->map(fn(array $item) => array_intersect_key($item, array_flip($keys)))->all();
        }
        else {
            $from = $this->map($key)->toArray();
        }

        return Hash::make($algo, $from);
    }

    /**
     * Pipe item value into class
     *
     * @template TGetDefault
     * @template TClass of class-string
     *
     * @param TKey $key
     * @param TClass $class
     * @param TGetDefault|(Closure(): TGetDefault) $default
     * @return TClass|TGetDefault
     */
    public function getPipedInto($key, string $class, mixed $default = null): mixed
    {
        return new $class($this->storage->get($key, $default));
    }

    /**
     * Pipe item value into class
     *
     * @template TGetDefault
     * @template TPipeReturnType
     *
     * @param TKey $key
     * @param callable(TValue|TGetDefault): TPipeReturnType $callback
     * @param TGetDefault|(Closure(): TGetDefault) $default
     * @return TPipeReturnType
     */
    public function getPiped($key, callable $callback, mixed $default = null): mixed
    {
        return $callback($this->storage->get($key, $default));
    }

    /**
     * Pipe item value into FollectionRows
     *
     * @template TGetDefault
     *
     * @param TKey $key
     * @param TGetDefault|(\Closure(): TGetDefault) $default
     * @return Rows
     */
    public function getRows($key, mixed $default = []): mixed
    {
        return $this->getPipedInto($key, Rows::class, $default);
    }

    public function copyIfExists(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            return $this;
        }

        return $this->copy($toKey, $sourceKey);
    }

    public function copy(string|int $toKey, string|int $sourceKey, mixed $default = null): static
    {
        if (!$this->has($sourceKey) && func_num_args() === 2) {
            throw Error::runtimeException("key('$sourceKey') does not exist")->with([
                'data' => $this->all()
            ]);
        }

        $this->storage->put($toKey, $this->storage->get($sourceKey, $default));

        return $this;
    }

    public function moveIfExists(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            return $this;
        }

        return $this->move($toKey, $sourceKey);
    }

    public function move(string|int $toKey, string|int $sourceKey, mixed $default = null): static
    {
        if (!$this->has($sourceKey) && func_num_args() === 2) {
            throw Error::runtimeException("key('$sourceKey') does not exist")->with([
                'data' => $this->all()
            ]);
        }
        $this->copy($toKey, $sourceKey, $default);
        $this->storage->forget($sourceKey);

        return $this;
    }


    /**
     * Get an item from the collection by key or add it to collection if it does not exist.
     *
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function getOrPut($key, $value)
    {
        throw new NotImplementedException("method('getOrPut') is not implemented");
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @param (callable(TValue, TKey): array-key)|array|string $groupBy
     * @param bool $preserveKeys
     * @return static<array-key, static<array-key, TValue>>
     */
    public function groupBy($groupBy, $preserveKeys = false): static
    {
        return $this->follection(
            $this->storage
                ->groupBy(
                    $this->makeCallback($groupBy),
                    $preserveKeys
                )
                ->map(fn($item) => $this->follection($item))
        );
    }

    /**
     * Key an associative array by a field or using a callback.
     *
     * @param (callable(TValue, TKey): array-key)|array|string $keyBy
     * @return static<array-key, TValue>
     */
    public function keyBy($keyBy): static
    {
        return $this->follection($this->storage->keyBy($this->makeCallback($keyBy)));
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param TKey|array<array-key, TKey> $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->storage->has($key);
    }

    /**
     * Determine if any of the keys exist in the collection.
     *
     * @param mixed $key
     * @return bool
     */
    public function hasAny($key)
    {
        return $this->storage->hasAny(...func_get_args());
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param callable|string $value
     * @param string|null $glue
     * @return string
     */
    public function implode($value, $glue = null)
    {
        return $this->storage->implode(...$this->mapArgumentsItemCallback(func_get_args()));
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function intersect($items): static
    {
        return $this->follection($this->clean()->intersect(...func_get_args()));
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function intersectByKeys($items): static
    {
        return $this->follection($this->clean()->intersectByKeys(...func_get_args()));
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->storage->isEmpty();
    }

    /**
     * Determine if the collection contains a single item.
     *
     * @return bool
     */
    public function containsOneItem(): bool
    {
        return $this->count() === 1;
    }

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param string $glue
     * @param string $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '')
    {
        return $this->clean()->join(...func_get_args());
    }

    /**
     * Get the keys of the collection items.
     *
     * @return Record<int, TKey>
     */
    public function keys(): Record
    {
        return new Record($this->storage->keys());
    }

    /**
     * Get the last item from the collection.
     *
     * @template TLastDefault
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TLastDefault|(Closure(): TLastDefault) $default
     * @return TValue|TLastDefault
     */
    public function last(callable $callback = null, $default = null)
    {
        return $this->kvt()->last(
            $this->makeCallback($callback),
            $default
        )?->transformItem($this);
    }

    /**
     * Get the values of a given key.
     *
     * @param string|int|array<array-key, string> $value
     * @param string|null $key
     * @return static<int, mixed>
     */
    public function pluck($value, $key = null): static
    {
        return $this->follection($this->storage->pluck(...func_get_args()));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): static
    {
        return $this->follection($this->storage->map($this->makeCallback($callback)));
    }

    /**
     * Run a dictionary map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapToDictionaryKey of array-key
     * @template TMapToDictionaryValue
     *
     * @param callable(TValue, TKey): array<TMapToDictionaryKey, TMapToDictionaryValue> $callback
     * @return static<TMapToDictionaryKey, array<int, TMapToDictionaryValue>>
     */
    public function mapToDictionary(callable $callback): static
    {
        return $this->follection($this->storage->mapToDictionary($this->makeCallback($callback)));
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param callable(TValue, TKey): array<TMapWithKeysKey, TMapWithKeysValue> $callback
     * @return static<TMapWithKeysKey, TMapWithKeysValue>
     */
    public function mapWithKeys(callable $callback): static
    {
        return $this->follection($this->storage->mapWithKeys($this->makeCallback($callback)));
    }

    /**
     * Merge the collection with the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function merge($items): static
    {
        return $this->follection($this->clean()->merge(...func_get_args()));
    }

    /**
     * Recursively merge the collection with the given items.
     *
     * @template TMergeRecursiveValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TMergeRecursiveValue>|iterable<TKey, TMergeRecursiveValue> $items
     * @return static<TKey, TValue|TMergeRecursiveValue>
     */
    public function mergeRecursive($items): static
    {
        return $this->follection($this->clean()->mergeRecursive(...func_get_args()));
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @template TCombineValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<array-key, TCombineValue>|iterable<array-key, TCombineValue> $values
     * @return static<TValue, TCombineValue>
     */
    public function combine($values): static
    {
        return $this->follection($this->clean()->combine(...func_get_args()));
    }

    /**
     * Union the collection with the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function union($items): static
    {
        return $this->follection($this->clean()->union(...func_get_args()));
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param int $step
     * @param int $offset
     * @return static
     */
    public function nth($step, $offset = 0): static
    {
        return $this->follection($this->storage->nth(...func_get_args()));
    }

    /**
     * Get the items with the specified keys.
     *
     * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
     * @return static
     */
    public function only($keys): static
    {
        return $this->follection($this->storage->only(...func_get_args()));
    }

    /**
     * Get and remove the last N items from the collection.
     *
     * @param int $count
     * @return static<int, TValue>|TValue|null
     */
    public function pop($count = 1): static
    {
        return $this->follection($this->storage->pop(...func_get_args()));
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param TValue $value
     * @param TKey $key
     * @return $this
     */
    public function prepend($value, $key = null): static
    {
        $this->storage->prepend(...func_get_args());

        return $this;
    }

    /**
     * Push one or more items onto the end of the collection.
     *
     * @param TValue ...$values
     * @return $this
     */
    public function push(...$values): static
    {
        $this->storage->push(...func_get_args());

        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param iterable<array-key, TValue> $source
     * @return static
     */
    public function concat($source): static
    {
        return $this->follection($this->storage->concat(...func_get_args()));
    }

    /**
     * Get and remove an item from the collection.
     *
     * @template TPullDefault
     *
     * @param TKey $key
     * @param TPullDefault|(Closure(): TPullDefault) $default
     * @return TValue|TPullDefault
     */
    public function pull($key, $default = null)
    {
        throw new NotImplementedException("method('pull') is not implemented");
    }

    /**
     * Put an item in the collection by key.
     *
     * @param TKey $key
     * @param TValue $value
     * @return $this
     */
    public function put($key, $value): static
    {
        $this->storage->put(...func_get_args());

        return $this;
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param int|null $number
     * @return static<int, TValue>|TValue
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null)
    {
        $random = $this->kvt()->random(...$this->mapArgumentsItemCallback(func_get_args()));
        if ($random instanceof LazyCollection) {
            return $this->follection($random->all());
        }
        return $random->transformItem($this);
    }

    /**
     * Replace the collection items with the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function replace($items): static
    {
        return $this->follection($this->storage->replace(...func_get_args()));
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @return static
     */
    public function replaceRecursive($items): static
    {
        return $this->follection($this->storage->replaceRecursive(...func_get_args()));
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): static
    {
        return $this->follection($this->storage->reverse(...func_get_args()));
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param TValue|(callable(TValue,TKey): bool) $value
     * @param bool $strict
     * @return TKey|bool
     */
    public function search($value, $strict = false)
    {
        throw new NotImplementedException("method('search') is not implemented");
    }

    /**
     * Get and remove the first N items from the collection.
     *
     * @param int $count
     * @return static<int, TValue>|TValue|null
     */
    public function shift($count = 1): static
    {
        return $this->follection($this->storage->shift(...func_get_args()));
    }

    /**
     * Shuffle the items in the collection.
     *
     * @param int|null $seed
     * @return static
     */
    public function shuffle($seed = null): static
    {
        return $this->follection($this->storage->shuffle(...func_get_args()));
    }

    /**
     * Create chunks representing a "sliding window" view of the items in the collection.
     *
     * @param int $size
     * @param int $step
     * @return static<int, static>
     */
    public function sliding($size = 2, $step = 1): static
    {
        return $this->follection($this->storage->sliding(...func_get_args()));
    }

    /**
     * Skip the first {$count} items.
     *
     * @param int $count
     * @return static
     */
    public function skip($count): static
    {
        return $this->follection($this->storage->skip(...func_get_args()));
    }

    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipUntil($value): static
    {
        return $this->follection($this->storage->skipUntil($this->makeCallback($value)));
    }

    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipWhile($value): static
    {
        return $this->follection($this->storage->skipWhile($this->makeCallback($value)));
    }

    /**
     * Slice the underlying collection array.
     *
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function slice($offset, $length = null): static
    {
        return $this->follection($this->storage->slice($offset, $length));
    }

    /**
     * Split a collection into a certain number of groups.
     *
     * @param int $numberOfGroups
     * @return static<int, static>
     */
    public function split($numberOfGroups): static
    {
        return $this->follection($this->storage->split(...func_get_args()));
    }

    /**
     * Split a collection into a certain number of groups, and fill the first groups completely.
     *
     * @param int $numberOfGroups
     * @return static<int, static>
     */
    public function splitIn($numberOfGroups): static
    {
        return $this->follection($this->storage->splitIn(...func_get_args()));
    }

    /**
     * Get the first item in the collection, but only if exactly one item exists. Otherwise, throw an exception.
     *
     * @param (callable(TValue, TKey): bool)|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     * @throws \Illuminate\Support\MultipleItemsFoundException
     */
    public function sole($key = null, $operator = null, $value = null)
    {
        return $this->kvt()->sole(...$this->mapArgumentsItemCallback(func_get_args()))?->transformItem($this);
    }

    /**
     * Get the first item in the collection but throw an exception if no matching items exist.
     *
     * @param (callable(TValue, TKey): bool)|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return TValue
     *
     * @throws \Illuminate\Support\ItemNotFoundException
     */
    public function firstOrFail($key = null, $operator = null, $value = null)
    {
        return $this->kvt()->firstOrFail(...$this->mapArgumentsItemCallback(func_get_args()))?->transformItem($this);
    }

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param int $size
     * @return static<int, static>
     */
    public function chunk($size): static
    {
        return $this->follection(
            $this->storage
                ->chunk($size)
                ->map(fn($item) => $this->follection($item))
        );
    }

    /**
     * Chunk the collection into chunks with a callback.
     *
     * @param callable(TValue, TKey, static<int, TValue>): bool $callback
     * @return static<int, static<int, TValue>>
     */
    public function chunkWhile(callable $callback): static
    {
        return $this->follection($this->storage->chunkWhile($this->makeCallback($callback)));
    }

    /**
     * Sort through each item with a callback.
     *
     * @param (callable(TValue, TValue): int)|null|int $callback
     * @return static
     */
    public function sort($callback = null): static
    {
        throw new NotImplementedException("method('sort') is not implemented");
    }

    /**
     * Sort items in descending order.
     *
     * @param int $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR): static
    {
        return $this->follection($this->clean()->sortDesc(...func_get_args()));
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string $callback
     * @param int $options
     * @param bool $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): static
    {
        throw new NotImplementedException("method('sortBy') is not implemented");
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string $callback
     * @param int $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): static
    {
        throw new NotImplementedException("method('sortByDesc') is not implemented");
    }

    /**
     * Sort the collection keys.
     *
     * @param int $options
     * @param bool $descending
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false): static
    {
        return $this->follection($this->clean()->sortKeys(...func_get_args()));
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param int $options
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR): static
    {
        return $this->follection($this->clean()->sortKeysDesc(...func_get_args()));
    }

    /**
     * Sort the collection keys using a callback.
     *
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function sortKeysUsing(callable $callback): static
    {
        return $this->follection($this->storage->sortKeysUsing($this->makeCallback($callback)));
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * @param int $offset
     * @param int|null $length
     * @param array<array-key, TValue> $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = []): static
    {
        return $this->follection($this->storage->splice(...func_get_args()));
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param int $limit
     * @return static
     */
    public function take($limit): static
    {
        return $this->follection($this->storage->take(...func_get_args()));
    }

    /**
     * Take items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeUntil($value): static
    {
        return $this->follection($this->takeUntil($this->makeCallback($value)));
    }

    /**
     * Take items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeWhile($value): static
    {
        return $this->follection($this->storage->takeWhile($this->makeCallback($value)));
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * @param callable(TValue, TKey): TValue $callback
     * @return $this
     */
    public function transform(callable $callback): static
    {
        $this->storage->transform($this->makeCallback($callback));

        return $this;
    }

    /**
     * Exchange items in storage
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null $items
     * @return static
     */
    public function exchange(mixed $items): static
    {
        $this->storage->exchange($items);

        return $this;
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @return static
     */
    public function undot(): static
    {
        return $this->follection($this->storage->undot());
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @param bool $strict
     * @return static
     */
    public function unique($key = null, $strict = false): static
    {
        return $this->follection($this->storage->unique($this->makeCallback($key), $strict));
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static<int, TValue>
     */
    public function values(): static
    {
        return $this->follection($this->storage->values());
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * e.g. new Collection([1, 2, 3])->zip([4, 5, 6]);
     *      => [[1, 4], [2, 5], [3, 6]]
     *
     * @template TZipValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<array-key, TZipValue>|iterable<array-key, TZipValue> ...$items
     * @return static<int, static<int, TValue|TZipValue>>
     */
    public function zip($items): static
    {
        return $this->follection($this->storage->zip(...func_get_args()));
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @template TPadValue
     *
     * @param int $size
     * @param TPadValue $value
     * @return static<int, TValue|TPadValue>
     */
    public function pad($size, $value): static
    {
        return $this->follection($this->storage->pad(...func_get_args()));
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new FollectionIterator($this);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->storage->count();
    }

    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null): static
    {
        return $this->follection($this->storage->countBy($this->makeCallback($countBy)));
    }

    /**
     * Add an item to the collection.
     *
     * @param TValue $item
     * @return $this
     */
    public function add($item): static
    {
        $this->storage->add($item);

        return $this;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return Collection<TKey, TValue>
     */
    public function toBase()
    {
        throw new NotImplementedException("method('toBase') is not implemented");
    }

    /** @inheritdoc */
    //TODO kas siin peaks üldse nii tegema, et alati tagastab reaalset Value, siis poleks justkui, Value Retriverit vaja?
    public function offsetGet(mixed $offset): mixed
    {
        return $this->getTransformedValue(
            $offset,
            static fn($key) => throw new ItemNotFoundException("item('$key') not found in collection")
        );
    }

    /** @inheritdoc */
    public function offsetSet($offset, $value): void
    {
        $this->storage->put($offset, $value);
    }

    /** @inheritdoc */
    public function offsetUnset(mixed $offset): void
    {
        $this->storage->forget($offset);
    }

    ############################################ enum

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TMakeKey, TMakeValue>|iterable<TMakeKey, TMakeValue>|null $items
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make($items = [])
    {
        throw new NotImplementedException("method('make') is not implemented");
    }

    /**
     * Wrap the given value in a collection if applicable.
     *
     * @template TWrapKey of array-key
     * @template TWrapValue
     *
     * @param iterable<TWrapKey, TWrapValue> $value
     * @return static<TWrapKey, TWrapValue>
     */
    public static function wrap($value)
    {
        throw new NotImplementedException("method('wrap') is not implemented");
    }

    /**
     * Get the underlying items from the given collection if applicable.
     *
     * @template TUnwrapKey of array-key
     * @template TUnwrapValue
     *
     * @param array<TUnwrapKey, TUnwrapValue>|static<TUnwrapKey, TUnwrapValue> $value
     * @return array<TUnwrapKey, TUnwrapValue>
     */
    public static function unwrap($value)
    {
        throw new NotImplementedException("method('unwrap') is not implemented");
    }

    /**
     * Create a new instance with no items.
     *
     * @return static
     */
    public static function empty()
    {
        throw new NotImplementedException("method('empty') is not implemented");
    }

    /**
     * Create a new collection by invoking the callback a given amount of times.
     *
     * @template TTimesValue
     *
     * @param int $number
     * @param (callable(int): TTimesValue)|null $callback
     * @return static<int, TTimesValue>
     */
    public static function times($number, callable $callback = null)
    {
        throw new NotImplementedException("method('times') is not implemented");
    }

    /**
     * Alias for the "avg" method.
     *
     * @param (callable(TValue): float|int)|string|null $callback
     * @return float|int|null
     */
    public function average($callback = null)
    {
        return $this->avg($callback);
    }

    /**
     * Alias for the "contains" method.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function some($key, $operator = null, $value = null)
    {
        return $this->contains(...func_get_args());
    }

    /**
     * Dump the items and end the script.
     *
     * @param mixed ...$args
     * @return never
     */
    public function dd(...$args)
    {
        $this->storage->dd();
    }

    /**
     * Dump the items.
     *
     * @return $this
     */
    public function dump(): string
    {
        return VarDumper::dump($this->toArray());
    }

    /**
     * Execute a callback over each item.
     *
     * @param callable(TValue, TKey): mixed $callback
     * @return $this
     */
    public function each(callable $callback): static
    {
        $this->storage->each($this->makeCallback($callback));

        return $this;
    }

    /**
     * Execute a callback over each nested chunk of items.
     *
     * @param callable(...mixed): mixed  $callback
     * @return static
     */
    public function eachSpread(callable $callback): static
    {
        $this->storage->eachSpread($this->makeCallback($callback));

        return $this;
    }

    /**
     * Determine if all items pass the given truth test.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null): bool
    {
        return $this->storage->every(...$this->mapArgumentsItemCallback(func_get_args()));
    }

    /**
     * Get the first item by the given key value pair.
     *
     * @param callable|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return TValue|null
     */
    public function firstWhere($key, $operator = null, $value = null)
    {
        return $this->kvt()->firstWhere(...$this->mapArgumentsItemCallback(func_get_args()))?->transformItem($this);
    }

    /**
     * Get a single key's value from the first matching item in the collection.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function value($key, mixed $default = null): mixed
    {
        return Item::clean($this->storage->value($key, $default));
    }

    /** @inheritDoc */
    public function valueAt($key, mixed $default = null): mixed
    {
        return Item::clean($this->storage->get($key, $default));
    }

    /**
     * Check is key empty or not
     *
     * @param $key
     * @return bool
     */
    public function hasValue($key): bool
    {
        if (!$this->storage->has($key)) {
            return false;
        }

        return Is::ok($this->valueAt($key, null));
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->storage->isNotEmpty();
    }

    /**
     * Run a map over each nested chunk of items.
     *
     * @template TMapSpreadValue
     *
     * @param callable(mixed): TMapSpreadValue $callback
     * @return static<TKey, TMapSpreadValue>
     */
    public function mapSpread(callable $callback)
    {
        throw new NotImplementedException("method('mapSpread') is not implemented");
    }

    /**
     * Run a grouping map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapToGroupsKey of array-key
     * @template TMapToGroupsValue
     *
     * @param callable(TValue, TKey): array<TMapToGroupsKey, TMapToGroupsValue> $callback
     * @return static<TMapToGroupsKey, static<int, TMapToGroupsValue>>
     */
    public function mapToGroups(callable $callback): static
    {
        return $this->follection(
            $this->storage
                ->mapToGroups($this->makeCallback($callback))
                ->map(fn($item) => $this->follection($item))
        );
    }

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param callable(TValue, TKey): mixed $callback
     * @return static<int, mixed>
     */
    public function flatMap(callable $callback): static
    {
        return $this->follection($this->storage->flatMap($this->makeCallback($callback)));
    }

    /**
     * Map the values into a new class.
     *
     * @template TMapIntoValue
     *
     * @param class-string<TMapIntoValue> $class
     * @return static<TKey, TMapIntoValue>
     */
    public function mapInto($class): static
    {
        return $this->follection($this->storage->mapInto(...func_get_args()));
    }

    /**
     * Get the min value of a given key.
     *
     * @param (callable(TValue):mixed)|string|null $callback
     * @return mixed
     */
    public function min($callback = null)
    {
        return $this->kvt()->min(
            $this->kvyValueRetriever($callback)
        );
    }

    /**
     * Get the max value of a given key.
     *
     * @param (callable(TValue):mixed)|string|null $callback
     * @return mixed
     */
    public function max($callback = null)
    {
        return $this->kvt()->max(
            $this->kvyValueRetriever($callback)
        );
    }

    /**
     * "Paginate" the collection by slicing it into a smaller collection.
     *
     * @param int $page
     * @param int $perPage
     * @return static
     */
    public function forPage($page, $perPage): static
    {
        return $this->follection($this->storage->forPage(...func_get_args()));
    }

    /**
     * Partition the collection into two arrays using the given callback or key.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param TValue|string|null $operator
     * @param TValue|null $value
     * @return static<int<0, 1>, static<TKey, TValue>>
     */
    public function partition($key, $operator = null, $value = null): static
    {
        return $this->follection($this->storage->partition(...$this->mapArgumentsItemCallback(func_get_args())));
    }

    /**
     * Get the sum of the given values.
     *
     * @param (callable(TValue): mixed)|string|null $callback
     * @return mixed
     */
    public function sum($callback = null)
    {
        return $this->kvt()->sum(
            $this->kvyValueRetriever($callback)
        );
    }

    /**
     * Apply the callback if the collection is empty.
     *
     * @template TWhenEmptyReturnType
     *
     * @param (callable($this): TWhenEmptyReturnType) $callback
     * @param (callable($this): TWhenEmptyReturnType)|null $default
     * @return $this|TWhenEmptyReturnType
     */
    public function whenEmpty(callable $callback, callable $default = null)
    {
        return $this->storage->whenEmpty(...func_get_args());
    }

    /**
     * Apply the callback if the collection is not empty.
     *
     * @template TWhenNotEmptyReturnType
     *
     * @param callable($this): TWhenNotEmptyReturnType $callback
     * @param (callable($this): TWhenNotEmptyReturnType)|null $default
     * @return $this|TWhenNotEmptyReturnType
     */
    public function whenNotEmpty(callable $callback, callable $default = null)
    {
        return $this->storage->whenNotEmpty(...func_get_args());
    }

    /**
     * Apply the callback unless the collection is empty.
     *
     * @template TUnlessEmptyReturnType
     *
     * @param callable($this): TUnlessEmptyReturnType $callback
     * @param (callable($this): TUnlessEmptyReturnType)|null $default
     * @return $this|TUnlessEmptyReturnType
     */
    public function unlessEmpty(callable $callback, callable $default = null)
    {
        return $this->storage->unlessEmpty(...func_get_args());
    }

    /**
     * Apply the callback unless the collection is not empty.
     *
     * @template TUnlessNotEmptyReturnType
     *
     * @param callable($this): TUnlessNotEmptyReturnType $callback
     * @param (callable($this): TUnlessNotEmptyReturnType)|null $default
     * @return $this|TUnlessNotEmptyReturnType
     */
    public function unlessNotEmpty(callable $callback, callable $default = null)
    {
        return $this->storage->unlessNotEmpty(...func_get_args());
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param callable|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($key, $operator = null, $value = null): static
    {
        return $this->follection($this->storage->where(...$this->mapArgumentsItemCallback(func_get_args())));
    }

    /**
     * Filter items where the value for the given key is null.
     *
     * @param string|null $key
     * @return static
     */
    public function whereNull($key = null): static
    {
        return $this->follection($this->storage->whereNull(...func_get_args()));
    }

    /**
     * Filter items where the value for the given key is not null.
     *
     * @param string|null $key
     * @return static
     */
    public function whereNotNull($key = null): static
    {
        return $this->follection($this->storage->whereNotNull(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function whereStrict($key, $value): static
    {
        return $this->follection($this->storage->whereStrict(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @param bool $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false): static
    {
        return $this->follection($this->storage->whereIn(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @return static
     */
    public function whereInStrict($key, $values): static
    {
        return $this->follection($this->storage->whereInStrict(...func_get_args()));
    }

    /**
     * Filter items such that the value of the given key is between the given values.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @return static
     */
    public function whereBetween($key, $values): static
    {
        return $this->follection($this->storage->whereBetween(...func_get_args()));
    }

    /**
     * Filter items such that the value of the given key is not between the given values.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @return static
     */
    public function whereNotBetween($key, $values): static
    {
        return $this->follection($this->storage->whereNotBetween(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false): static
    {
        return $this->follection($this->storage->whereNotIn(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param \Illuminate\Contracts\Support\Arrayable|iterable $values
     * @return static
     */
    public function whereNotInStrict($key, $values): static
    {
        return $this->follection($this->storage->whereNotInStrict(...func_get_args()));
    }

    /**
     * Filter the items, removing any items that don't match the given type(s).
     *
     * @template TWhereInstanceOf
     *
     * @param class-string<TWhereInstanceOf>|array<array-key, class-string<TWhereInstanceOf>> $type
     * @return static<TKey, TWhereInstanceOf>
     */
    public function whereInstanceOf($type): static
    {
        return $this->follection($this->storage->whereInstanceOf(...func_get_args()));
    }

    /**
     * Pass the collection to the given callback and return the result.
     *
     * @template TPipeReturnType
     *
     * @param callable($this): TPipeReturnType $callback
     * @return TPipeReturnType
     */
    public function pipe(callable $callback)
    {
        return $this->makeCallback($callback)($this);
    }

    /**
     * Pass the collection into a new class.
     *
     * @param class-string $class
     * @return mixed
     */
    public function pipeInto($class): mixed
    {
        return new $class($this);
    }

    /**
     * Pass the collection through a series of callable pipes and return the result.
     *
     * @param callable[] $callbacks
     * @return mixed
     */
    public function pipeThrough($callbacks): mixed
    {
        return Collection::make($callbacks)->reduce(
            function ($carry, $callback) {
                return $this->makeCallback($callback)($carry);
            },
            $this,
        );
    }

    /**
     * Reduce the collection to a single value.
     *
     * @template TReduceInitial
     * @template TReduceReturnType
     *
     * @param callable(TReduceInitial|TReduceReturnType, TValue, TKey): TReduceReturnType $callback
     * @param TReduceInitial $initial
     * @return TReduceReturnType
     */
    public function reduce(callable $callback, $initial = null): mixed
    {
        return $this->storage->reduce(
            $this->makeCallback($callback),
            $initial
        );
    }

    /**
     * Reduce the collection to multiple aggregate values.
     *
     * @param callable $callback
     * @param mixed ...$initial
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public function reduceSpread(callable $callback, ...$initial)
    {
        return $this->storage->reduceSpread($this->makeCallback($callback), ...$initial);
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param (callable(TValue, TKey): bool)|bool $callback
     * @return static
     */
    public function reject($callback = true)
    {
        return $this->follection($this->storage->reject($this->makeCallback($callback)));
    }

    /**
     * Pass the collection to the given callback and then return it.
     *
     * @param callable($this): mixed $callback
     * @return $this
     */
    public function tap(callable $callback): static
    {
        $this->storage->tap($this->makeCallback($callback));

        return $this;
    }

    /**
     * Return only unique items from the collection array using strict comparison.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @return static
     */
    public function uniqueStrict($key = null)
    {
        return $this->follection($this->storage->uniqueStrict($this->makeCallback($key)));
    }

    /**
     * Collect the values into a collection.
     *
     * @return Collection<TKey, TValue>
     */
    public function collect(): Collection
    {
        return $this->storage->collect();
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array<TKey, mixed>
     */
    public function toArray(): array
    {
        return $this->storage->map([Item::class, 'clean'])->all();
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<TKey, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return $this->storage->toJson($options);
    }

    /**
     * Get a CachingIterator instance.
     *
     * @param int $flags
     * @return \CachingIterator
     */
    public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING)
    {
        throw new NotImplementedException("method('getCachingIterator') is not implemented");
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->escapeWhenCastingToString
            ? e($this->toJson())
            : $this->toJson();
    }

    /**
     * Indicate that the model's string representation should be escaped when __toString is invoked.
     *
     * @param bool $escape
     * @return $this
     */
    public function escapeWhenCastingToString($escape = true): static
    {
        $this->escapeWhenCastingToString = $escape;

        return $this;
    }

    /**
     * Add a method to the list of proxied methods.
     *
     * @param string $method
     * @return void
     */
    public static function proxy($method)
    {
        throw new NotImplementedException("method('proxy') is not implemented");
    }

    /**
     * Apply the callback if the given "value" is (or resolves to) truthy.
     *
     * @template TWhenParameter
     * @template TWhenReturnType
     *
     * @param (Closure($this): TWhenParameter)|TWhenParameter|null $value
     * @param (callable($this, TWhenParameter): TWhenReturnType)|null $callback
     * @param (callable($this, TWhenParameter): TWhenReturnType)|null $default
     * @return $this|TWhenReturnType
     */
    public function when($value = null, callable $callback = null, callable $default = null)
    {
        return $this->storage->when(...func_get_args());
    }

    public function whenHas(string $name, callable $has, mixed $default = null): void
    {
        $has = $this->makeCallback($has);
        if (!$this->storage->has($name)) {
            if (func_num_args() > 2) {
                $has($default);
            }

            return;
        }
        $has($this->get($name));
    }

    /**
     * Apply the callback if the given "value" is (or resolves to) falsy.
     *
     * @template TUnlessParameter
     * @template TUnlessReturnType
     *
     * @param (Closure($this): TUnlessParameter)|TUnlessParameter|null $value
     * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $callback
     * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $default
     * @return $this|TUnlessReturnType
     */
    public function unless($value = null, callable $callback = null, callable $default = null)
    {
        return $this->storage->unless(...func_get_args());
    }

    /** @inheritDoc */
    public function ok(string|int $key = null): bool
    {
        if ($key === null) {
            return ($this->storage->count() > 0);
        }

        if (!$this->storage->has($key)) {
            return false;
        }

        return Is::ok($this->valueAt($key));
    }

    /** @inheritDoc */
    public function notOk(string|int $key = null): bool
    {
        return !$this->ok($key);
    }

    public function debug(string $name = null, bool $asArray = false): void
    {
        if ($name) {
            debug([$name => $asArray ? $this->toArray() : $this]);
        }
        else {
            debug($asArray ? $this->toArray() : $this);
        }
    }

    public function debugAll(string $name = null): void
    {
        $this->debug($name, true);
    }

    protected function useAsCallable($value): bool
    {
        return !is_string($value) && is_callable($value);
    }

    private function follection(Enumerable|array $value): static
    {
        if ($value instanceof Enumerable) {
            $data = $value->all();
        }
        else {
            $data = $value;
        }
        if (is_array($data)) {
            $data = array_map(static function ($item) {
                if ($item instanceof KeyValueItem) {
                    return $item->value();
                }

                return $item;
            }, $data);
        }

        return $this->new($data);
    }

    /**
     * @return LazyCollection<TKey, KeyValueItem>
     */
    protected function kvt(): LazyCollection
    {
        return $this->storage
            ->lazy()
            ->map(fn($item, $key) => new KeyValueItem($item, $key));
    }

    /**
     * @return LazyCollection<TKey, TValue>
     */
    protected function clean(): LazyCollection
    {
        return $this->storage
            ->lazy()
            ->map([Item::class, 'clean']);
    }
}
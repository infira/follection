<?php

namespace Infira\Collection;

/**
 * * @template TKey of array-key
 * * @template TValue
 */
class InjectableCollection extends \Illuminate\Support\Collection
{
    use extensions\Inject;

    /**
     * Get the average value of a given key.
     *
     * @param (callable(TValue): float|int)|string|null $callback
     * @return float|int|null
     */
    public function avg($callback = null)
    {
        return parent::avg(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Determine if an item exists in the collection.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function contains($key = null, $operator = null, $value = null)
    {
        return parent::contains(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue> $items
     * @param callable(TValue, TValue): int $callback
     * @return static
     */
    public function diffUsing($items = null, callable $callback = null)
    {
        return parent::diffUsing($items, \Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function diffAssocUsing($items = null, callable $callback = null)
    {
        return parent::diffAssocUsing($items, \Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function diffKeysUsing($items = null, callable $callback = null)
    {
        return parent::diffKeysUsing($items, \Infira\Collection\helpers\InjectableHelper::make($callback));
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
        return parent::duplicates(\Infira\Collection\helpers\InjectableHelper::makeIf($callback), $strict);
    }


    /**
     * Retrieve duplicate items from the collection using strict comparison.
     *
     * @param (callable(TValue): bool)|string|null $callback
     * @return static
     */
    public function duplicatesStrict($callback = null)
    {
        return parent::duplicatesStrict(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Run a filter over each of the items.
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        return parent::filter(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @template TFirstDefault
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TFirstDefault|(\Closure(): TFirstDefault) $default
     * @return TValue|TFirstDefault
     */
    public function first(callable $callback = null, $default = null)
    {
        return parent::first(\Infira\Collection\helpers\InjectableHelper::make($callback), $default);
    }


    /**
     * Group an associative array by a field or using a callback.
     *
     * @param (callable(TValue, TKey): array-key)|array|string $groupBy
     * @param bool $preserveKeys
     * @return static<array-key, static<array-key, TValue>>
     */
    public function groupBy($groupBy = null, $preserveKeys = false)
    {
        return parent::groupBy(\Infira\Collection\helpers\InjectableHelper::makeIf($groupBy), $preserveKeys);
    }


    /**
     * Key an associative array by a field or using a callback.
     *
     * @param (callable(TValue, TKey): array-key)|array|string $keyBy
     * @return static<array-key, TValue>
     */
    public function keyBy($keyBy = null)
    {
        return parent::keyBy(\Infira\Collection\helpers\InjectableHelper::makeIf($keyBy));
    }


    /**
     * Concatenate values of a given key as a string.
     *
     * @param callable|string $value
     * @param string|null $glue
     * @return string
     */
    public function implode($value = null, $glue = null)
    {
        return parent::implode(\Infira\Collection\helpers\InjectableHelper::makeIf($value), $glue);
    }


    /**
     * Get the last item from the collection.
     *
     * @template TLastDefault
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TLastDefault|(\Closure(): TLastDefault) $default
     * @return TValue|TLastDefault
     */
    public function last(callable $callback = null, $default = null)
    {
        return parent::last(\Infira\Collection\helpers\InjectableHelper::make($callback), $default);
    }


    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback = null)
    {
        return parent::map(\Infira\Collection\helpers\InjectableHelper::make($callback));
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
    public function mapToDictionary(callable $callback = null)
    {
        return parent::mapToDictionary(\Infira\Collection\helpers\InjectableHelper::make($callback));
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
    public function mapWithKeys(callable $callback = null)
    {
        return parent::mapWithKeys(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param (callable(TValue): int)|int|null $number
     * @return static<int, TValue>|TValue
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null)
    {
        return parent::random(\Infira\Collection\helpers\InjectableHelper::makeIf($number));
    }


    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param TValue|(callable(TValue,TKey): bool) $value
     * @param bool $strict
     * @return TKey|bool
     */
    public function search($value = null, $strict = false)
    {
        return parent::search(\Infira\Collection\helpers\InjectableHelper::makeIf($value), $strict);
    }


    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipUntil($value = null)
    {
        return parent::skipUntil(\Infira\Collection\helpers\InjectableHelper::makeIf($value));
    }


    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipWhile($value = null)
    {
        return parent::skipWhile(\Infira\Collection\helpers\InjectableHelper::makeIf($value));
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
        return parent::sole(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
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
        return parent::firstOrFail(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Chunk the collection into chunks with a callback.
     *
     * @param callable(TValue, TKey, static<int, TValue>): bool $callback
     * @return static<int, static<int, TValue>>
     */
    public function chunkWhile(callable $callback = null)
    {
        return parent::chunkWhile(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Sort through each item with a callback.
     *
     * @param (callable(TValue, TValue): int)|null|int $callback
     * @return static
     */
    public function sort($callback = null)
    {
        return parent::sort(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Sort the collection using the given callback.
     *
     * @param array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string $callback
     * @param int $options
     * @param bool $descending
     * @return static
     */
    public function sortBy($callback = null, $options = SORT_REGULAR, $descending = false)
    {
        return parent::sortBy(\Infira\Collection\helpers\InjectableHelper::makeIf($callback), $options, $descending);
    }


    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param array<array-key, (callable(TValue, TValue): mixed)|(callable(TValue, TKey): mixed)|string|array{string, string}>|(callable(TValue, TKey): mixed)|string $callback
     * @param int $options
     * @return static
     */
    public function sortByDesc($callback = null, $options = SORT_REGULAR)
    {
        return parent::sortByDesc(\Infira\Collection\helpers\InjectableHelper::makeIf($callback), $options);
    }


    /**
     * Sort the collection keys using a callback.
     *
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function sortKeysUsing(callable $callback = null)
    {
        return parent::sortKeysUsing(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Take items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeUntil($value = null)
    {
        return parent::takeUntil(\Infira\Collection\helpers\InjectableHelper::makeIf($value));
    }


    /**
     * Take items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeWhile($value = null)
    {
        return parent::takeWhile(\Infira\Collection\helpers\InjectableHelper::makeIf($value));
    }


    /**
     * Transform each item in the collection using a callback.
     *
     * @param callable(TValue, TKey): TValue $callback
     * @return $this
     */
    public function transform(callable $callback = null)
    {
        return parent::transform(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Return only unique items from the collection array.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @param bool $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        return parent::unique(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $strict);
    }


    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null)
    {
        return parent::countBy(\Infira\Collection\helpers\InjectableHelper::makeIf($countBy));
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
    public static function times($number = null, callable $callback = null)
    {
        return parent::times($number, \Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Alias for the "avg" method.
     *
     * @param (callable(TValue): float|int)|string|null $callback
     * @return float|int|null
     */
    public function average($callback = null)
    {
        return parent::average(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Alias for the "contains" method.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function some($key = null, $operator = null, $value = null)
    {
        return parent::some(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Determine if an item exists, using strict comparison.
     *
     * @param (callable(TValue): bool)|TValue|array-key $key
     * @param TValue|null $value
     * @return bool
     */
    public function containsStrict($key = null, $value = null)
    {
        return parent::containsStrict(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $value);
    }


    /**
     * Execute a callback over each item.
     *
     * @param callable(TValue, TKey): mixed $callback
     * @return $this
     */
    public function each(callable $callback = null)
    {
        return parent::each(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Execute a callback over each nested chunk of items.
     *
     * @param callable(...mixed): mixed  $callback
     * @return static
     */
    public function eachSpread(callable $callback = null)
    {
        return parent::eachSpread(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Determine if all items pass the given truth test.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function every($key = null, $operator = null, $value = null)
    {
        return parent::every(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Get the first item by the given key value pair.
     *
     * @param callable|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return TValue|null
     */
    public function firstWhere($key = null, $operator = null, $value = null)
    {
        return parent::firstWhere(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Run a map over each nested chunk of items.
     *
     * @template TMapSpreadValue
     *
     * @param callable(mixed): TMapSpreadValue $callback
     * @return static<TKey, TMapSpreadValue>
     */
    public function mapSpread(callable $callback = null)
    {
        return parent::mapSpread(\Infira\Collection\helpers\InjectableHelper::make($callback));
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
    public function mapToGroups(callable $callback = null)
    {
        return parent::mapToGroups(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param callable(TValue, TKey): mixed $callback
     * @return static<int, mixed>
     */
    public function flatMap(callable $callback = null)
    {
        return parent::flatMap(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Get the min value of a given key.
     *
     * @param (callable(TValue):mixed)|string|null $callback
     * @return TValue
     */
    public function min($callback = null)
    {
        return parent::min(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Get the max value of a given key.
     *
     * @param (callable(TValue):mixed)|string|null $callback
     * @return TValue
     */
    public function max($callback = null)
    {
        return parent::max(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Partition the collection into two arrays using the given callback or key.
     *
     * @param (callable(TValue, TKey): bool)|TValue|string $key
     * @param TValue|string|null $operator
     * @param TValue|null $value
     * @return static<int<0, 1>, static<TKey, TValue>>
     */
    public function partition($key = null, $operator = null, $value = null)
    {
        return parent::partition(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Get the sum of the given values.
     *
     * @param (callable(TValue): mixed)|string|null $callback
     * @return mixed
     */
    public function sum($callback = null)
    {
        return parent::sum(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
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
    public function whenEmpty(callable $callback = null, callable $default = null)
    {
        return parent::whenEmpty(\Infira\Collection\helpers\InjectableHelper::make($callback), \Infira\Collection\helpers\InjectableHelper::make($default));
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
    public function whenNotEmpty(callable $callback = null, callable $default = null)
    {
        return parent::whenNotEmpty(\Infira\Collection\helpers\InjectableHelper::make($callback), \Infira\Collection\helpers\InjectableHelper::make($default));
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
    public function unlessEmpty(callable $callback = null, callable $default = null)
    {
        return parent::unlessEmpty(\Infira\Collection\helpers\InjectableHelper::make($callback), \Infira\Collection\helpers\InjectableHelper::make($default));
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
    public function unlessNotEmpty(callable $callback = null, callable $default = null)
    {
        return parent::unlessNotEmpty(\Infira\Collection\helpers\InjectableHelper::make($callback), \Infira\Collection\helpers\InjectableHelper::make($default));
    }


    /**
     * Filter items by the given key value pair.
     *
     * @param callable|string $key
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($key = null, $operator = null, $value = null)
    {
        return parent::where(\Infira\Collection\helpers\InjectableHelper::makeIf($key), $operator, $value);
    }


    /**
     * Pass the collection to the given callback and return the result.
     *
     * @template TPipeReturnType
     *
     * @param callable($this): TPipeReturnType $callback
     * @return TPipeReturnType
     */
    public function pipe(callable $callback = null)
    {
        return parent::pipe(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Pass the collection through a series of callable pipes and return the result.
     *
     * @param array<callable> $callbacks
     * @return mixed
     */
    public function pipeThrough($callbacks = null)
    {
        return parent::pipeThrough(\Infira\Collection\helpers\InjectableHelper::makeIf($callbacks));
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
    public function reduce(callable $callback = null, $initial = null)
    {
        return parent::reduce(\Infira\Collection\helpers\InjectableHelper::make($callback), $initial);
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
    public function reduceSpread(callable $callback = null, ...$initial)
    {
        return parent::reduceSpread(\Infira\Collection\helpers\InjectableHelper::make($callback), $initial);
    }


    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param (callable(TValue, TKey): bool)|bool $callback
     * @return static
     */
    public function reject($callback = true)
    {
        return parent::reject(\Infira\Collection\helpers\InjectableHelper::makeIf($callback));
    }


    /**
     * Pass the collection to the given callback and then return it.
     *
     * @param callable($this): mixed $callback
     * @return $this
     */
    public function tap(callable $callback = null)
    {
        return parent::tap(\Infira\Collection\helpers\InjectableHelper::make($callback));
    }


    /**
     * Return only unique items from the collection array using strict comparison.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @return static
     */
    public function uniqueStrict($key = null)
    {
        return parent::uniqueStrict(\Infira\Collection\helpers\InjectableHelper::makeIf($key));
    }
}

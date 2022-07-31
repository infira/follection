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
        return parent::avg($this->_makeInjectableIf($callback));
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
        return parent::contains($this->_makeInjectableIf($key), $operator, $value);
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
        return parent::diffUsing($items, $this->_makeInjectable($callback));
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
        return parent::diffAssocUsing($items, $this->_makeInjectable($callback));
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
        return parent::diffKeysUsing($items, $this->_makeInjectable($callback));
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
        return parent::duplicates($this->_makeInjectableIf($callback), $strict);
    }


    /**
     * Retrieve duplicate items from the collection using strict comparison.
     *
     * @param (callable(TValue): bool)|string|null $callback
     * @return static
     */
    public function duplicatesStrict($callback = null)
    {
        return parent::duplicatesStrict($this->_makeInjectableIf($callback));
    }


    /**
     * Run a filter over each of the items.
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        return parent::filter($this->_makeInjectable($callback));
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
        return parent::first($this->_makeInjectable($callback), $default);
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
        return parent::groupBy($this->_makeInjectableIf($groupBy), $preserveKeys);
    }


    /**
     * Key an associative array by a field or using a callback.
     *
     * @param (callable(TValue, TKey): array-key)|array|string $keyBy
     * @return static<array-key, TValue>
     */
    public function keyBy($keyBy = null)
    {
        return parent::keyBy($this->_makeInjectableIf($keyBy));
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
        return parent::implode($this->_makeInjectableIf($value), $glue);
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
        return parent::last($this->_makeInjectable($callback), $default);
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
        return parent::map($this->_makeInjectable($callback));
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
        return parent::mapToDictionary($this->_makeInjectable($callback));
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
        return parent::mapWithKeys($this->_makeInjectable($callback));
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
        return parent::random($this->_makeInjectableIf($number));
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
        return parent::search($this->_makeInjectableIf($value), $strict);
    }


    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipUntil($value = null)
    {
        return parent::skipUntil($this->_makeInjectableIf($value));
    }


    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function skipWhile($value = null)
    {
        return parent::skipWhile($this->_makeInjectableIf($value));
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
        return parent::sole($this->_makeInjectableIf($key), $operator, $value);
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
        return parent::firstOrFail($this->_makeInjectableIf($key), $operator, $value);
    }


    /**
     * Chunk the collection into chunks with a callback.
     *
     * @param callable(TValue, TKey, static<int, TValue>): bool $callback
     * @return static<int, static<int, TValue>>
     */
    public function chunkWhile(callable $callback = null)
    {
        return parent::chunkWhile($this->_makeInjectable($callback));
    }


    /**
     * Sort through each item with a callback.
     *
     * @param (callable(TValue, TValue): int)|null|int $callback
     * @return static
     */
    public function sort($callback = null)
    {
        return parent::sort($this->_makeInjectableIf($callback));
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
        return parent::sortBy($this->_makeInjectableIf($callback), $options, $descending);
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
        return parent::sortByDesc($this->_makeInjectableIf($callback), $options);
    }


    /**
     * Sort the collection keys using a callback.
     *
     * @param callable(TKey, TKey): int $callback
     * @return static
     */
    public function sortKeysUsing(callable $callback = null)
    {
        return parent::sortKeysUsing($this->_makeInjectable($callback));
    }


    /**
     * Take items in the collection until the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeUntil($value = null)
    {
        return parent::takeUntil($this->_makeInjectableIf($value));
    }


    /**
     * Take items in the collection while the given condition is met.
     *
     * @param TValue|callable(TValue,TKey): bool $value
     * @return static
     */
    public function takeWhile($value = null)
    {
        return parent::takeWhile($this->_makeInjectableIf($value));
    }


    /**
     * Transform each item in the collection using a callback.
     *
     * @param callable(TValue, TKey): TValue $callback
     * @return $this
     */
    public function transform(callable $callback = null)
    {
        return parent::transform($this->_makeInjectable($callback));
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
        return parent::unique($this->_makeInjectableIf($key), $strict);
    }


    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param (callable(TValue, TKey): mixed)|string|null $countBy
     * @return static<array-key, int>
     */
    public function countBy($countBy = null)
    {
        return parent::countBy($this->_makeInjectableIf($countBy));
    }
}

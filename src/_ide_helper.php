<?php

/**
 * This is IDE helper for suggest/autocomplete
 * @noinspection all
 */

namespace Illuminate\Support;

/**
 * @template TKey of array-key
 * @template TValue
 */
class Collection
{
	/**
	 * Map values into Collection with optional callback
	 * if $callback is null then regular mapInto(\Illuminate\Support\Collection) is called
	 *
	 * @template TMapIntoValue
	 * @template TClassValue - class-string<TValue, TKey>
	 * @template TMapValue
	 *
	 * @param callable(TClassValue): TMapValue $callback
	 * @return static<TKey, TMapValue>
	 * @see \Illuminate\Support\Collection::map()
	 * @see \Illuminate\Support\Collection::mapInto()
	 */
	public function mapCollect($callback = null): static
	{
		/** @see \Infira\Collection\extensions\MapCollect::mapCollect() */;
	}


	/**
	 * Get the items with the specified keys for each member current collection item
	 *
	 * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::only()
	 */
	public function mapOnly($keys): static
	{
		/** @see \Infira\Collection\extensions\MapOnly::mapOnly() */;
	}


	/**
	 * Map values into static::class and uses callback
	 *
	 * @template TMapValue
	 *
	 * @param callable(static::class): TMapValue $callback
	 * @return static<TKey, TValue>
	 * @see \Illuminate\Support\Collection::map()
	 * @see \Illuminate\Support\Collection::mapInto()
	 */
	public function mapSelf($callback = null): static
	{
		/** @see \Infira\Collection\extensions\MapSelf::mapSelf() */;
	}


	/**
	 * Map values into class with callback
	 * if $callback is null then regular mapInto is called
	 * ex: $callback(new $class(TValue, TKey)
	 *
	 * @template TMapIntoValue
	 * @template TClassValue - class-string<TValue, TKey>
	 * @template TMapValue
	 *
	 * @param class-string<TValue, TKey> $class
	 * @param callable(TClassValue): TMapValue $callback
	 *
	 * @return static<TKey, TMapValue>
	 * @see \Illuminate\Support\Collection::map()
	 * @see \Illuminate\Support\Collection::mapInto()
	 */
	public function mapWith($class, $callback = null): static
	{
		/** @see \Infira\Collection\extensions\MapWith::mapWith() */;
	}


	/**
	 * Merge the collection with the given items and only keys present in $items, or provide $keys with parameter
	 *
	 * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
	 * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::only()
	 * @see \Illuminate\Support\Collection::merge()
	 */
	public function mergeOnly($items, $keys = null): static
	{
		/** @see \Infira\Collection\extensions\MergeOnly::mergeOnly() */;
	}


	/**
	 * zips collection using own keys and values
	 *
	 * @template TZipValue
	 *
	 * @return static<int, static<int, TValue|TZipValue>>
	 */
	public function zipSelf(): static
	{
		/** @see \Infira\Collection\extensions\ZipSelf::zipSelf() */;
	}
}

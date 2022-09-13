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
	 * Chain methods dynamically
	 *
	 * @template TMethod - collection method
	 * @template TArguments - mixed[]
	 *
	 * @param array<TMethod, TArguments>|array<TMethod, array<TArguments>>|\Illuminate\Contracts\Support\Arrayable<array<TMethod, TArguments>> $chain
	 * @return static
	 * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/chain.md
	 * @see \Infira\Collection\extensions\Chain::chain()
	 * @see \Infira\Collection\CollectionMacros::chain()
	 */
	public function chain($chain)
	{
	}


	/**
	 * Rename array keys with new one while maintains array order
	 * @template TGetDefault
	 * @template TTKey of array-key - copy to key
	 * @template TFKey of array-key - copy from key
	 *
	 * @param TFKey|array<TFKey,TTKey> $fromKey
	 * @param TTKey|null $toKey
	 * @param TGetDefault|(\Closure(): TGetDefault) $default
	 *
	 * @return static
	 * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/copy.md
	 *
	 * @see \Infira\Collection\extensions\Copy::copy()
	 * @see \Infira\Collection\CollectionMacros::copy()
	 */
	public function copy(string|int|array $fromKey, string|int|null $toKey = null, mixed $default = null)
	{
	}


	/**
	 * Remove an item from the collection by key or using callback
	 *
	 * @param TKey|array<array-key, TKey>|callable(TValue, TKey): bool $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::forget()
	 * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/forgetBy.md
	 * @see \Infira\Collection\extensions\ForgetBy::forgetBy()
	 * @see \Infira\Collection\CollectionMacros::forgetBy()
	 */
	public function forgetBy($keys): static
	{
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
	 * @see \Infira\Collection\extensions\Inject::inject()
	 * @see \Infira\Collection\CollectionMacros::inject()
	 */
	public function inject(callable $callback, string $method = 'map')
	{
	}


	/**
	 * @see \Infira\Collection\extensions\Inject::toInjectable()
	 * @see \Infira\Collection\CollectionMacros::toInjectable()
	 */
	public function toInjectable(): \Infira\Collection\InjectableCollection
	{
	}


	/**
	 * Map values into Collection with optional callback
	 * if is_callable($keys) then regular \Illuminate\Support\Collection->keyBy()
	 *
	 * @param array<TKey>|string|callable $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::keyBy()
	 * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/keysBy.md
	 * @see \Infira\Collection\extensions\KeysBy::keysBy()
	 * @see \Infira\Collection\CollectionMacros::keysBy()
	 */
	public function keysBy($keys, string $glue = '.')
	{
	}


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
	 * @see \Infira\Collection\extensions\MapCollect::mapCollect()
	 * @see \Infira\Collection\CollectionMacros::mapCollect()
	 */
	public function mapCollect(callable $callback = null)
	{
	}


	/**
	 * Get the items with the specified keys for each member current collection item
	 *
	 * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::only()
	 * @see \Infira\Collection\extensions\MapOnly::mapOnly()
	 * @see \Infira\Collection\CollectionMacros::mapOnly()
	 */
	public function mapOnly($keys): static
	{
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
	 * @see \Infira\Collection\extensions\MapSelf::mapSelf()
	 * @see \Infira\Collection\CollectionMacros::mapSelf()
	 */
	public function mapSelf(callable $callback = null)
	{
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
	 * @see \Infira\Collection\extensions\MapWith::mapWith()
	 * @see \Infira\Collection\CollectionMacros::mapWith()
	 */
	public function mapWith(string $class, callable $callback = null)
	{
	}


	/**
	 * Merge the collection with the given items and only keys present in $items, or provide $keys with parameter
	 *
	 * @param \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue> $items
	 * @param \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string $keys
	 * @return static
	 * @see \Illuminate\Support\Collection::only()
	 * @see \Illuminate\Support\Collection::merge()
	 * @see \Infira\Collection\extensions\MergeOnly::mergeOnly()
	 * @see \Infira\Collection\CollectionMacros::mergeOnly()
	 */
	public function mergeOnly($items, array $keys = null)
	{
	}


	/**
	 * PipeInto using static::class
	 *
	 * @return static
	 * @see \Illuminate\Support\Collection::pipeInto()
	 * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/pipeIntoSelf.md
	 * @see \Infira\Collection\extensions\PipeIntoSelf::pipeIntoSelf()
	 * @see \Infira\Collection\CollectionMacros::pipeIntoSelf()
	 */
	public function pipeIntoSelf(): static
	{
	}


	/**
	 * Rename array keys with new one while maintains array order
	 *
	 * @param TKey|array<TKey => TKey> $from
	 * @param TKey $to = null
	 *
	 * @return static
	 * @example rename('from-key','to-key')
	 * @example rename(['from-key-1'=>'to-key-1', 'from-key-2'=>'to-key-2'])
	 *
	 * @see \Infira\Collection\extensions\Rename::rename()
	 * @see \Infira\Collection\CollectionMacros::rename()
	 */
	public function rename($from, $to = null)
	{
	}


	/**
	 * zips collection using own keys and values
	 *
	 * @template TZipValue
	 *
	 * @return static<int, static<int, TValue|TZipValue>>
	 * @see \Infira\Collection\extensions\ZipSelf::zipSelf()
	 * @see \Infira\Collection\CollectionMacros::zipSelf()
	 */
	public function zipSelf()
	{
	}
}

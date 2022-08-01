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
     */
    public function chain($chain)
    {
        /** @see \Infira\Collection\extensions\Chain::chain() */;
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
     */
    public function copy(string|int|array $fromKey, string|int|null $toKey = null, mixed $default = null)
    {
        /** @see \Infira\Collection\extensions\Copy::copy() */;
    }


    /**
     * Remove an item from the collection by key or using callback
     *
     * @param TKey|array<array-key, TKey>|callable(TValue, TKey): bool $keys
     * @return static
     * @see \Illuminate\Support\Collection::forget()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/forgetBy.md
     */
    public function forgetBy($keys): static
    {
        /** @see \Infira\Collection\extensions\ForgetBy::forgetBy() */;
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
        /** @see \Infira\Collection\extensions\Inject::inject() */;
    }


    public function toInjectable(): \Infira\Collection\InjectableCollection
    {
        /** @see \Infira\Collection\extensions\Inject::toInjectable() */;
    }


    /**
     * Map values into Collection with optional callback
     * if is_callable($keys) then regular \Illuminate\Support\Collection->keyBy()
     *
     * @param array<TKey>|string|callable $keys
     * @return static
     * @see \Illuminate\Support\Collection::keyBy()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/keysBy.md
     */
    public function keysBy($keys, string $glue = '.')
    {
        /** @see \Infira\Collection\extensions\KeysBy::keysBy() */;
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
     */
    public function mapCollect(callable $callback = null)
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
    public function mapSelf(callable $callback = null)
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
    public function mapWith(string $class, callable $callback = null)
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
    public function mergeOnly($items, array $keys = null)
    {
        /** @see \Infira\Collection\extensions\MergeOnly::mergeOnly() */;
    }


    /**
     * PipeInto using static::class
     *
     * @return static
     * @see \Illuminate\Support\Collection::pipeInto()
     * @see https://github.com/infira/laravel-collection-extensions/blob/main/docs/pipeIntoSelf.md
     */
    public function pipeIntoSelf(): static
    {
        /** @see \Infira\Collection\extensions\PipeIntoSelf::pipeIntoSelf() */;
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
     */
    public function rename($from, $to = null)
    {
        /** @see \Infira\Collection\extensions\Rename::rename() */;
    }


    /**
     * zips collection using own keys and values
     *
     * @template TZipValue
     *
     * @return static<int, static<int, TValue|TZipValue>>
     */
    public function zipSelf()
    {
        /** @see \Infira\Collection\extensions\ZipSelf::zipSelf() */;
    }
}

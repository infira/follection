<?php

namespace Infira\Collection;

/**
 * @mixin \Illuminate\Support\Collection
 */
class CollectionMacros
{
    public static function chain(): \Closure
    {
        return function ($chain) {
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
        };
    }


    public static function forgetBy(): \Closure
    {
        return function ($keys) {
            if (is_string($keys) || is_array($keys)) {
                return $this->forget($keys);
            }
            foreach ($this as $key => $value) {
                if ($keys($value, $key)) {
                    $this->forget($key);
                }
            }

            return $this;
        };
    }


    public static function inject(): \Closure
    {
        return function ($callback, $method = 'map') {
            return $this->$method(\Infira\Collection\helpers\InjectableHelper::make($callback));
        };
    }


    public static function toInjectable(): \Closure
    {
        return function () {
            return $this->pipeInto(\Infira\Collection\InjectableCollection::class);
        };
    }


    public static function keysBy(): \Closure
    {
        return function ($keys, $glue = '.') {
            if (is_callable($keys)) {
                return $this->keyBy($keys);
            }
            if (is_string($keys)) {
                $keys = array_map('trim', explode('.', $keys));
            }

            return $this->keyBy(function ($item) use ($keys, $glue) {
                return collect($item)->only($keys)->join($glue);
            });
        };
    }


    public static function mapCollect(): \Closure
    {
        return function ($callback = null) {
            if (!$callback) {
                return $this->mapInto(\Illuminate\Support\Collection::class);
            }

            return $this->map(fn($value, $key) => $callback(new \Illuminate\Support\Collection($value, $key)));
        };
    }


    public static function mapOnly(): \Closure
    {
        return function ($keys) {
            return $this->map(fn($item) => collect($item)->only($keys)->all());
        };
    }


    public static function mapSelf(): \Closure
    {
        return function ($callback = null) {
            return $this->map(fn($value, $key) => $callback(new static($value, $key)));
        };
    }


    public static function mapWith(): \Closure
    {
        return function ($class, $callback = null) {
            if (!$callback) {
                return $this->mapInto($class);
            }

            return $this->map(fn($value, $key) => $callback(new $class($value, $key)));
        };
    }


    public static function mergeOnly(): \Closure
    {
        return function ($items, $keys = null) {
            $items = $this->getArrayableItems($items);
            if ($keys === null) {
                $keys = array_keys($items);
            }
            else {
                if ($keys instanceof \Illuminate\Support\Enumerable) {
                    $keys = $keys->all();
                }
                $keys = is_array($keys) ? $keys : array_slice(func_get_args(), 1);
            }

            return new static(array_merge(array_flip($keys), \Illuminate\Support\Arr::only($items, $keys)));
        };
    }


    public static function pipeIntoSelf(): \Closure
    {
        return function () {
            return $this->pipeInto(static::class);
        };
    }


    public static function rename(): \Closure
    {
        return function ($from, $to = null) {
            $from = (is_string($from) and is_string($to)) ? [$from, $to] : $this->getArrayableItems($from);

            $this->items = array_combine(
                array_map(static fn($key) => $from[$key] ?? $key, array_keys($this->items)),
                $this->items);

            return $this;
        };
    }


    public static function zipSelf(): \Closure
    {
        return function () {
            return $this->keys()->zip($this->values());
        };
    }
}

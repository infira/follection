<?php

namespace Infira\Collection\helpers;

class InjectableHelper
{
    public static function getPHPBuiltInTypes(): array
    {
        // PHP 8.1
        if (\PHP_VERSION_ID >= 80100) {
            return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object', 'mixed', 'false', 'null', 'never'];
        }

        // PHP 8
        if (\PHP_MAJOR_VERSION === 8) {
            return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object', 'mixed', 'false', 'null'];
        }

        // PHP 7
        switch (\PHP_MINOR_VERSION) {
            case 0:
                return ['array', 'callable', 'string', 'int', 'bool', 'float'];
            case 1:
                return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void'];
            default:
                return ['array', 'callable', 'string', 'int', 'bool', 'float', 'iterable', 'void', 'object'];
        }
    }

    public static function make(callable $callback): \Closure
    {
        $ref = new \ReflectionFunction($callback);
        $types = array_map(static fn(\ReflectionParameter $p) => $p->getType()?->getName(), $ref->getParameters());

        $cast = static function ($params) use ($types): array {
            array_walk($params, function (&$value, $key) use ($types) {
                $type = $types[$key] ?? null;
                if ($type && !($value instanceof $type) && !in_array($type, static::getPHPBuiltInTypes(), true)) {
                    $value = new $type($value);
                }
            }, $params);

            return $params;
        };

        return fn(...$params) => $callback(...$cast($params));
    }

    public static function makeIf(mixed $callback): mixed
    {
        if (is_array($callback)) {
            return array_map(fn($cb) => static::makeIf($cb), $callback);
        }
        if (is_callable($callback)) {
            return static::make($callback);
        }

        return $callback;
    }
}
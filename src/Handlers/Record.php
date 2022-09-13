<?php

namespace Infira\Follection\Handlers;


use Infira\FluentValue\FluentValue;
use Infira\Follection\FollectionTransformer;

/**
 * @method Field|mixed last(callable $callback = null, $default = null)
 * @method Field|mixed first(callable $callback = null, $default = null)
 * @method Field|mixed get($key, $default = null)
 * @method Field offsetGet(mixed $key)
 */
class Record extends FollectionTransformer
{
    protected string $itemTransformerClass = Field::class;

    public function transformItem(mixed $value, int|string $key = null, string $transformClass = null): mixed
    {
        $item = parent::transformItem($value, $key, $transformClass);
        if ($item instanceof FluentValue) {
            $item->setAttribute('parentKey', $key);
            $item->onChange(function ($value) use ($key) {
//                debug([
//                    "onChange" => $key,
//                    $value,
//                    $this->storage->get($key),
//                    getTrace(4)
//                ]);
                if ($key !== null) {
                    $this->storage->put($key, $value);
                }
            });
        }

        return $item;
    }
}
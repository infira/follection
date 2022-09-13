<?php
/**
 * @noinspection PhpInstanceofIsAlwaysTrueInspection
 * @noinspection PhpUnused
 */

namespace Infira\Follection\Traits;


use App\Support\Flu;
use Illuminate\Support\Collection;
use Infira\Error\Error;
use Infira\Follection\FollectionTransformer;
use Wolo\Is;
use Wolo\VarDumper;

/**
 * @uses  FollectionTransformer
 * @uses  Collection
 */
trait CollectionExtras
{
    use CollectionOf;

    private function doGetValue(string|int $key, mixed $default = null)
    {
        if ($this instanceof FollectionTransformer) {
            return $this->valueAt($key, $default);
        }

        return $this->get($key, $default);
    }

    public function flush(): static
    {
        $this->items = [];

        return $this;
    }

    public function exchange(mixed $items): static
    {
        $this->items = $this->getArrayableItems($items);

        return $this;
    }

    public function buildQuery(): string
    {
        return http_build_query($this->all());
    }

    public static function formUrlStr(string|object $str): static
    {
        if (is_string($str)) {
            return static::of(Flu::parseStr($str));
        }

        return static::of($str);
    }

    public function copyIfExists(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            return $this;
        }

        return $this->copy($toKey, $sourceKey);
    }

    public function copy(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            throw Error::runtimeException("key('$sourceKey') does not exist")->with([
                'data' => $this->all()
            ]);
        }

        return $this->put($toKey, $this->doGetValue($sourceKey));
    }

    public function moveIfExists(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            return $this;
        }

        return $this->move($toKey, $sourceKey);
    }

    public function move(string|int $toKey, string|int $sourceKey): static
    {
        if (!$this->has($sourceKey)) {
            throw Error::runtimeException("key('$sourceKey') does not exist")->with([
                'data' => $this->all()
            ]);
        }
        $this->copy($toKey, $sourceKey);
        $this->forget($sourceKey);

        return $this;
    }

    public function isKeyNotEmpty(string|int $key): bool
    {
        return !$this->isKeyEmpty($key);
    }

    public function isKeyEmpty(string|int $key): bool
    {
        if (!$this->has($key)) {
            return true;
        }
        $val = $this->doGetValue($key);

        if (is_string($val)) {
            return empty(trim($val));
        }

        return empty($val);
    }

    public function ok(string|int $key = null): bool
    {
        if ($key === null) {
            return ($this->count() > 0);
        }
        if (!$this->has($key)) {
            return false;
        }

        return Is::ok($this->doGetValue($key));
    }

    public function notOk(string|int $key = null): bool
    {
        return !$this->ok($key);
    }

    public function size(): int
    {
        return $this->count();
    }

    public function debugSelf(): void
    {
        debug($this->all());
    }

    public function debug(): void
    {
        if ($this instanceof FollectionTransformer) {
            debug($this->toArray());
        }
        else {
            debug($this->all());
        }
    }

    public function dump(): string
    {
        return VarDumper::dump($this->toArray());
    }

    public function pre(): string
    {
        if ($this instanceof FollectionTransformer) {
            return VarDumper::pre($this->toArray());
        }

        return VarDumper::pre($this->all());
    }

    public function clone(): static
    {
        return clone $this;
    }

    public function whenHas(string $name, callable $has, mixed $default = null): void
    {
        if (!$this->has($name)) {
            if (func_num_args() > 2) {
                $has($default);
            }

            return;
        }
        $has($this->get($name));
    }

    public function new(): static
    {
        return new static($this);
    }
}
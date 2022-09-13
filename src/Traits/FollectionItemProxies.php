<?php
/** @noinspection PhpUnused */

namespace Infira\Follection\Traits;


use Illuminate\Support\Str;
use Infira\Follection\FollectionTransformer;

/**
 * @mixin FollectionTransformer
 */
trait FollectionItemProxies
{
    private array $proxyCache = [];
    private array $voidProxyCaches = [];

    protected static function getAttributeProxies(): array
    {
        return [];
    }

    public function hasProxy(string|int $key): bool
    {
        return isset(static::getAttributeProxies()[$key]) || $this->hasProxyMethod($key);
    }

    protected function hasProxyMethod(string $key): bool
    {
        return method_exists($this, 'proxy'.Str::studly($key));
    }

    public function getProxy(string|int $key): mixed
    {
        if (array_key_exists($key, $this->proxyCache)) {
            return $this->proxyCache[$key];
        }
        $attributeProxies = static::getAttributeProxies();
        if (isset($attributeProxies[$key])) {
            $value = $this->useCallable($attributeProxies[$key]);
        }
        else {
            $value = $this->{'proxy'.Str::studly($key)}(
                $this->storage->get($key, null)
            );
        }

        if ($this->isProxyCacheVoided($key)) {
            return $value;
        }

        return $this->proxyCache[$key] = $value;
    }

    public function voidProxyCache(string|array $key): static
    {
        foreach ((array)$key as $k) {
            $this->voidProxyCaches[$k] = true;
        }

        return $this;
    }

    private function isProxyCacheVoided(string|int $key): bool
    {
        return $this->voidProxyCaches[$key] ?? false;
    }
}
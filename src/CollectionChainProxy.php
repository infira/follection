<?php

namespace Infira\Collection;


use contracts\CollectionChainTargetContract;
use Illuminate\Support\Collection;

class CollectionChainProxy
{
    /**
     * @var callable[]
     */
    private array $callables = [];
    private mixed $target;
    private bool $brakes = false;

    public function __construct(
        public Collection $collection,
        null|callable|CollectionChainTargetContract $target
    ) {
        $this->target = $target;
    }

    public function __call(string $name, array $arguments)
    {
//        $this->callables[] = (fn($carry) => $carry->$name(...$arguments))->bindTo(null);
        $this->callables[] = [$name, $arguments];

        return $this;
    }

    public function __invoke(string $method = 'map')
    {
        return $this->execute($method);
    }

    public function __debugInfo(): ?array
    {
        return [$this->callables];
    }

    private function getTarget(mixed $item): CollectionChainTargetContract|Collection
    {
        if ($this->target === null) {
            $class = $this->collection::class;

            return new $class($item);
        }
        $target = $this->target;

        return $target($item);
    }

    public function execute(string $method = 'map')
    {
        return $this->collection->{$method}(function ($item) {
            $carry = $this->getTarget($item);

            foreach ($this->callables as $callable) {
                [$method, $arguments] = $callable;
                $carry = $carry->$method(...$arguments);
                if ($this->brakes) {
                    break;
                }
            }
            if ($carry instanceof Collection) {
                return $carry->all();
            }

            return $carry->getOutput();
        });
    }

    public function brake(bool|callable $condition): static
    {
        $this->brakes = is_callable($condition) ? $condition($this->collection) : $condition;

        return $this;
    }
}
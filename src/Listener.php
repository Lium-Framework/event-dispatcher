<?php

declare(strict_types=1);

namespace Lium\EventDispatcher;

use Lium\EventDispatcher\Exception\InvalidListener;

final class Listener
{
    /** @var callable */
    private $callable;
    private ?string $type;
    private bool $alwaysMatching;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->type = null;

        $reflectionParameter = $this->extractReflectionParameter($callable);

        $reflectionType = $reflectionParameter->getType();
        assert($reflectionType instanceof \ReflectionNamedType);
        $this->type = $reflectionType->getName();
        if (!$this->isValidType($this->type)) {
            throw new InvalidListener($callable);
        }

        $this->alwaysMatching = \in_array($this->type, [null, 'object'], true);
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function match(object $event): bool
    {
        return $this->alwaysMatching || $event instanceof $this->type;
    }

    private function extractReflectionParameter(callable $callable): \ReflectionParameter
    {
        $closure = \Closure::fromCallable($callable);
        $reflectionFunction = new \ReflectionFunction($closure);

        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener($callable);
        }

        return $reflectionParameter;
    }

    private function isValidType(string $type):bool
    {
        if ($type === 'object') {
            return true;
        }
        return class_exists($type) || interface_exists($type);
    }
}

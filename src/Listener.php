<?php

declare(strict_types=1);

namespace Lium\EventDispatcher;

use Lium\EventDispatcher\Exception\InvalidListener;

final class Listener
{
    /** @var callable */
    private $callable;

    /** @var string|null */
    private $type;

    /** @var bool */
    private $alwaysMatching;

    public function __construct(callable $callable)
    {
        $reflectionParameter = $this->extractReflectionParameter($callable);

        $reflectionType = $reflectionParameter->getType();
        if ($reflectionType !== null) {
            $this->type = $reflectionType->getName();
            if ($this->type !== 'object' && $reflectionParameter->getClass() === null) {
                throw new InvalidListener($callable);
            }
        }

        $this->callable = $callable;
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
}

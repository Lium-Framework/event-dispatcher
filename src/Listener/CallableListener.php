<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use Closure;
use InvalidArgumentException;
use Lium\EventDispatcher\Exception\InvalidListener;
use Lium\EventDispatcher\Utils\Reflection\ReflectionType;
use ReflectionFunction;
use function in_array;

final class CallableListener implements Listener
{
    /** @var callable */
    private $callable;
    private int $priority;
    private ?string $type;
    private bool $alwaysMatching;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->priority = 0;
        $this->type = $this->extractListenerFirstParameterType($callable);
        $this->alwaysMatching = in_array($this->type, [null, 'object'], true);
    }

    public function match(object $event): bool
    {
        return $this->alwaysMatching || $event instanceof $this->type;
    }

    public function __invoke(object $event): void
    {
        $callable = $this->callable;

        $callable($event);
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    private function extractListenerFirstParameterType(callable $callable): ?string
    {
        $closure = Closure::fromCallable($callable);
        $reflectionFunction = new ReflectionFunction($closure);

        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener($callable);
        }

        try {
            $type = ReflectionType::findReflectionNamedType($reflectionParameter);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidListener($callable);
        }

        if ($type !== 'object' && $reflectionParameter->getClass() === null) {
            throw new InvalidListener($callable);
        }

        return $type;
    }
}

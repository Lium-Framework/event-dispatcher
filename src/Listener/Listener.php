<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use Lium\EventDispatcher\Exception\InvalidListener;

final class Listener
{
    /** @var callable */
    private $callable;

    /** @var ListenerParameter */
    private $parameter;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;

        $closure = \Closure::fromCallable($callable);
        $reflectionFunction = new \ReflectionFunction($closure);

        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener($callable);
        }

        try {
            $this->parameter = new ListenerParameter($reflectionParameter);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidListener($callable);
        }
    }

    public function matchEvent(object $event): bool
    {
        return $this->parameter->matchEvent($event);
    }
}

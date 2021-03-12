<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use Closure;
use ReflectionFunction;

final class CallableListener extends ReflectionParameterListener
{
    /** @var callable */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $closure = Closure::fromCallable($callable);

        parent::__construct(new ReflectionFunction($closure));
    }

    public function __invoke(object $event): void
    {
        $callable = $this->callable;

        $callable($event);
    }
}

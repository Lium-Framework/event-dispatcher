<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Exception\InvalidListener;

trait DefaultListenerProviderBehavior
{
    /** @var array<callable> */
    private $listeners;

    /** @var array<string>|null */
    private $listenerArgumentMap;

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->listenerArgumentMap === null) {
            // Prepare the map
            $this->listenerArgumentMap = [];
            foreach ($this->listeners as $key => $listener) {
                $this->listenerArgumentMap[$key] = $this->getListenerUniqueParameterType($listener);
            }
        }

        $listenersForEvent = [];
        foreach ($this->listeners as $key => $listener) {
            $listenerArgumentType = $this->listenerArgumentMap[$key];

            if ($event instanceof $listenerArgumentType || $listenerArgumentType === 'object') {
                $listenersForEvent[] = $listener;
            }
        }

        return $listenersForEvent;
    }

    /**
     * Prepare a map between a listener key and the listener first parameter class name thanks to \ReflectionFunction.
     * If the listener has no parameter or if this parameter is not type-hinted with a class name or with "object", the
     * listener is removed from the stack.
     *
     * @param callable $listener
     *
     * @return string
     * @throws \ReflectionException
     */
    private function getListenerUniqueParameterType(callable $listener): string
    {
        $closure = \Closure::fromCallable($listener);
        $reflectionFunction = new \ReflectionFunction($closure);

        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener($listener);
        }

        $type = $reflectionParameter->getType();
        if ($type === null) {
            throw new InvalidListener($listener);
        }

        $typeName = $type->getName();
        if ($typeName !== 'object' && $reflectionParameter->getClass() !== null) {
            throw new InvalidListener($listener);
        }

        return $typeName;
    }
}

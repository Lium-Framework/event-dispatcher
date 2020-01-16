<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Exception\InvalidListener;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A default listener parameter implementation.
 * It will check the listener first argument type to determine if the listener match the event.
 */
final class DefaultListenerProvider implements ListenerProviderInterface
{
    /** @var array<callable> */
    private $listeners;

    /** @var array<string>|null */
    private $listenerArgumentMap;

    /**
     * @param iterable<callable> $listeners
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function __construct(iterable $listeners)
    {
        if ($listeners instanceof \Traversable) {
            $listeners = iterator_to_array($listeners);
        }

        $this->listeners = $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->listenerArgumentMap === null) {
            $this->prepareListenerParameterMap();
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
     * @throws \ReflectionException
     */
    private function prepareListenerParameterMap(): void
    {
        $this->listenerArgumentMap = [];

        foreach ($this->listeners as $key => $listener) {
            $closure = \Closure::fromCallable($listener);
            $reflectionFunction = new \ReflectionFunction($closure);

            $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
            if ($reflectionParameter === null) {
                throw new InvalidListener($listener, 'The listener must have one argument corresponding to the event it listen.');
            }

            $type = $reflectionParameter->getType();
            if ($type === null) {
                throw new InvalidListener($listener, 'The listener argument must have a type corresponding to the event it listen.');
            }

            $typeName = $type->getName();
            if ($typeName !== 'object' && $reflectionParameter->getClass() !== null) {
                throw new InvalidListener($listener, 'The listener argument must have the type of the event it listen or the scalar type "object".');
            }

            $this->listenerArgumentMap[$key] = $typeName;
        }
    }
}

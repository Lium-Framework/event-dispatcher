<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Exception\InvalidListenerException;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A default listener parameter implementation.
 * It will check the listener first argument type to determine if the listener match the event.
 */
final class DefaultListenerProvider implements ListenerProviderInterface
{
    /** @var callable[] */
    private $listeners;

    /** @var string[]|null */
    private $listenerArgumentMap;

    /** @var array<string, array<callable>> */
    private $runtimeStorage;

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

        $this->listeners =  $listeners;
        $this->runtimeStorage = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (null === $this->listenerArgumentMap) {
            $this->prepareListenerParameterMap();
        }

        $eventName = get_class($event);

        if (!isset($this->runtimeStorage[$eventName])) {
            $this->runtimeStorage[$eventName] = [];

            foreach ($this->listeners as $key => $listener) {
                $listenerArgumentType = $this->listenerArgumentMap[$key];

                if ($event instanceof $listenerArgumentType || 'object' === $listenerArgumentType) {
                    $this->runtimeStorage[$eventName][] = $listener;
                }
            }
        }

        return $this->runtimeStorage[$eventName];
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
        $this->runtimeStorage = [];
        $this->listenerArgumentMap = [];

        foreach ($this->listeners as $key => $listener) {
            $closure = \Closure::fromCallable($listener);
            $reflectionFunction = new \ReflectionFunction($closure);

            if (null === $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null) {
                throw new InvalidListenerException($listener, 'The listener must have one argument corresponding to the event it listen.');
            }

            if (null === $type = $reflectionParameter->getType()) {
                throw new InvalidListenerException($listener, 'The listener argument must have a type corresponding to the event it listen.');
            }

            $typeName = $type->getName();
            if ('object' !== $typeName && !$reflectionParameter->getClass()) {
                throw new InvalidListenerException($listener, 'The listener argument must have a type of the event it listen or the scalar type "object".');
                continue;
            }

            $this->listenerArgumentMap[$key] = $typeName;
        }
    }
}

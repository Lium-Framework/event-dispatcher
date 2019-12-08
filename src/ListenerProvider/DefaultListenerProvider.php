<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

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

            if (!$reflectionParameter = $reflectionFunction->getParameters()[0] ?? null) {
                unset($this->listeners[$key]);
                continue;
            }

            if (null === $type = $reflectionParameter->getType()) {
                unset($this->listeners[$key]);
                continue;
            }

            $typeName = $type->getName();
            if ('object' !== $typeName && !$reflectionParameter->getClass()) {
                unset($this->listeners[$key]);
                continue;
            }

            $this->listenerArgumentMap[$key] = $typeName;
        }
    }
}

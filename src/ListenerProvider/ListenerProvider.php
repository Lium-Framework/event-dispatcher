<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * The strict listener provider implementation as explained in the PSR-14.
 */
final class ListenerProvider implements ListenerProviderInterface
{
    /** @var iterable<callable> */
    private $listeners;

    /** @var string[] */
    private $listenerParameterMap;

    /** @var array<string, iterable<callable>> */
    private $cachedListeners;

    /**
     * @param iterable<callable> $listeners
     */
    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (null === $this->listenerParameterMap) {
            $this->prepareListenerParameterMap();
        }

        $eventName = get_class($event);

        if (!isset($this->cachedListeners[$eventName])) {
            $this->cachedListeners[$eventName] = [];
            foreach ($this->listeners as $key => $listener) {
                if (is_a($event, $this->listenerParameterMap[$key])) {
                    $this->cachedListeners[$eventName][] = $listener;
                }
            }
        }

        return $this->cachedListeners[$eventName];
    }

    /**
     * This method is here to allow to change the listeners after the initialization in an immutable way
     *
     * @param iterable $listeners
     *
     * @return $this
     */
    public function withListeners(iterable $listeners): self
    {
        $new = clone $this;
        $new->listeners = $listeners;
        $new->cachedListeners = null;
        $new->listenerParameterMap = null;

        return $new;
    }

    /**
     * @throws \ReflectionException
     */
    private function prepareListenerParameterMap(): void
    {
        $this->cachedListeners = [];
        $this->listenerParameterMap = [];

        foreach ($this->listeners as $key => $listener) {
            $closure = \Closure::fromCallable($listener);
            $reflectionFunction = new \ReflectionFunction($closure);

            if (!$reflectionParameter = $reflectionFunction->getParameters()[0] ?? null) {
                continue;
            }

            if (!$class = $reflectionParameter->getClass()) {
                continue;
            }

            $this->listenerParameterMap[$key] = $class->getName();
        }
    }
}

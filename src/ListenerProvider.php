<?php

declare(strict_types=1);

namespace Helium\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /** @var iterable */
    private $listeners;

    /** @var array */
    private $cachedListeners;

    /** @var \ReflectionFunction[] */
    private $listenersParameterMap;

    public function __construct(iterable $listeners)
    {
        $this->initListeners($listeners);
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);
        get_parent_class($event);

        if (!isset($this->cachedListeners[$eventName])) {
            $this->cachedListeners[$eventName] = [];

            foreach ($this->listeners as $key => $listener) {
                if (is_a($event, $this->listenersParameterMap[$key])) {
                    $this->cachedListeners[$eventName][] = $listener;
                }
            }
        }

        dump($this->cachedListeners);

        return $this->cachedListeners[$eventName];
    }

    public function initListeners(iterable $listeners): void
    {
        $this->listeners = $listeners;
        $this->cachedListeners = [];
        $this->listenersParameterMap = [];

        foreach ($listeners as $key => $listener) {
            $closure = \Closure::fromCallable($listener);
            $reflectionFunction = new \ReflectionFunction($closure);

            if (!$reflectionParameter = $reflectionFunction->getParameters()[0] ?? null) {
                continue;
            }

            if (!$class = $reflectionParameter->getClass()) {
                continue;
            }

            $this->listenersParameterMap[$key] = $class->getName();
        }
    }
}

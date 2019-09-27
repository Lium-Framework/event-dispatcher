<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

final class ListenerProvider implements ListenerProviderInterface
{
    /** @var iterable */
    private $listeners;

    /** @var string[] */
    private $listenersParameterMap;

    /** @var array */
    private $cachedListeners;

    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (null === $this->listenersParameterMap) {
            $this->registerListeners($this->listeners);
        }

        $eventName = get_class($event);

        if (!isset($this->cachedListeners[$eventName])) {
            $this->cachedListeners[$eventName] = [];
            foreach ($this->listeners as $key => $listener) {
                if (is_a($event, $this->listenersParameterMap[$key])) {
                    $this->cachedListeners[$eventName][] = $listener;
                }
            }
        }

        dump(sprintf("----- Listeners for event %s -----", $eventName), $this->cachedListeners[$eventName]);

        return $this->cachedListeners[$eventName];
    }

    public function registerListeners(iterable $listeners): void
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

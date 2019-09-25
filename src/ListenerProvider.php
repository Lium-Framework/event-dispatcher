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
    private $cachedListenersReflectionFunctions;

    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
        $this->cachedListeners = [];
        $this->cachedListenersReflectionFunctions = [];

        foreach ($listeners as $key => $listener) {
            $closure = \Closure::fromCallable($listener);
            $this->cachedListenersReflectionFunctions[$key] = new \ReflectionFunction($closure);
        }
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
                $reflectionFunction = $this->cachedListenersReflectionFunctions[$key];

                if (!$reflectionParameter = $reflectionFunction->getParameters()[0] ?? null) {
                    continue;
                }

                if (!$class = $reflectionParameter->getClass()) {
                    continue;
                }

                if (is_a($event, $class->getName())) {
                    $this->cachedListeners[$eventName][] = $listener;
                }
            }
        }

        dump($this->cachedListeners);

        return $this->cachedListeners[$eventName];
    }
}

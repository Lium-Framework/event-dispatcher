<?php

declare(strict_types=1);

namespace Helium\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var ListenerProviderInterface */
    private $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process.
     *
     * @return object
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event): object
    {
        $listeners = $this->listenerProvider->getListenersForEvent($event);
        $eventIsStoppable = $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($eventIsStoppable && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}

<?php

declare(strict_types=1);

namespace Helium\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * The strict PSR-14 event dispatcher implementation.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /** @var ListenerProviderInterface */
    private $listenerProvider;

    /**
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(object $event): object
    {
        /** @var callable[] $listeners */
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

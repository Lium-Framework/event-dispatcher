<?php

declare(strict_types=1);

namespace Lium\EventDispatcher;

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

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedMethodCall
     */
    public function dispatch(object $event): object
    {
        $eventIsStoppable = $event instanceof StoppableEventInterface;
        if ($eventIsStoppable && $event->isPropagationStopped()) {
            return $event;
        }

        /** @var callable $listener */
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);

            if ($eventIsStoppable && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}

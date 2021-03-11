<?php

declare(strict_types=1);

namespace Lium\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * The PSR-14 event dispatcher implementation.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedMethodCall
     */
    public function dispatch(object $event): object
    {
        $eventIsStoppable = $event instanceof StoppableEventInterface;

        /** @var callable $listener */
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            if ($eventIsStoppable && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}

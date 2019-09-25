<?php

declare(strict_types=1);

namespace Helium\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /** @var iterable */
    private $listeners;

    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     *
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);

    }
}

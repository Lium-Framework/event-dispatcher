<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener;

trait DefaultListenerProviderBehavior
{
    /** @var array<Listener> */
    private $listeners;

    /**
     * @param iterable<callable> $listeners
     */
    public function __construct(iterable $listeners)
    {
        $this->listeners = [];
        foreach ($listeners as $listener) {
            $this->listeners[] = new Listener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listenerForEvent = array_filter(
            $this->listeners,
            static function (Listener $listener) use ($event) {
                return $listener->match($event);
            }
        );

        return array_map(
            static function (Listener $listener) {
                return $listener->getCallable();
            },
            $listenerForEvent
        );
    }
}

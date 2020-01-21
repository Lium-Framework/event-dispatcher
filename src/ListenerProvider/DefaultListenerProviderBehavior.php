<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener\Listener;

trait DefaultListenerProviderBehavior
{
    /** @var array<callable> */
    private $listeners;

    /** @var array<Listener>|null */
    private $listenersObjects;

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->listenersObjects === null) {
            // Prepare the map
            $this->listenersObjects = array_map(
                [$this, 'initListener'],
                $this->listeners
            );
        }

        return array_filter($this->listeners, static function (callable $listener) use ($event) {
            return $listener->matchEvent($event);
        });
    }

    private function initListener(callable $listener): Listener
    {
        return new Listener($listener);
    }
}

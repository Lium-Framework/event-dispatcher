<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener\CallableListener;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A default listener parameter implementation.
 * It will check the listener first parameter type to determine if the listener match the event.
 */
final class CallableListenerProvider implements ListenerProviderInterface
{
    /** @var array<CallableListener> */
    private array $listeners;

    /**
     * @param iterable<callable> $listeners
     */
    public function __construct(iterable $listeners)
    {
        $this->listeners = [];
        foreach ($listeners as $listener) {
            $this->listeners[] = new CallableListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $listener) {
            if ($listener->match($event) === false) {
                continue;
            }

            yield $listener;
        }
    }
}

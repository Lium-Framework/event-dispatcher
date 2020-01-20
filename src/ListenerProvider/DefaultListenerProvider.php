<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A default listener parameter implementation.
 * It will check the listener first parameter type to determine if the listener match the event.
 */
final class DefaultListenerProvider implements ListenerProviderInterface
{
    use DefaultListenerProviderBehavior;

    /**
     * @param iterable<callable> $listeners
     */
    public function __construct(iterable $listeners)
    {
        if ($listeners instanceof \Traversable) {
            $listeners = iterator_to_array($listeners);
        }

        $this->listeners = $listeners;
    }
}

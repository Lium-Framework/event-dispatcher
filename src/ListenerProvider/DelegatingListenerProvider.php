<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This ListenerProvider delegates its responsibilities to other listeners providers and store the results.
 */
final class DelegatingListenerProvider implements ListenerProviderInterface
{
    /** @var ListenerProviderInterface[] */
    private $subListenerProviders;

    /** @var array<string, array<callable>> */
    private $listenersForEventsStorage;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     */
    public function __construct(iterable $subListenerProviders)
    {
        $this->subListenerProviders = $subListenerProviders instanceof \Traversable
            ? iterator_to_array($subListenerProviders)
            : $subListenerProviders;
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);

        if (!isset($this->listenersForEventsStorage[$eventName])) {
            $this->listenersForEventsStorage[$eventName] = [];
            foreach ($this->subListenerProviders as $subListenerProvider) {
                $this->listenersForEventsStorage[$eventName][] = $subListenerProvider->getListenersForEvent($event);
            }

            $this->listenersForEventsStorage[$eventName] = array_merge(...$this->listenersForEventsStorage[$eventName]);
        }

        return $this->listenersForEventsStorage[$eventName];
    }
}

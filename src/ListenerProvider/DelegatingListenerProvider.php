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
        $this->subListenerProviders = $this->iterableToArray($subListenerProviders);
        $this->listenersForEventsStorage = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);

        if (!isset($this->listenersForEventsStorage[$eventName])) {
            $this->listenersForEventsStorage[$eventName] = [];

            $temporaryListenersForEvent = [];
            foreach ($this->subListenerProviders as $subListenerProvider) {
                $temporaryListenersForEvent[] = $this->iterableToArray(
                    $subListenerProvider->getListenersForEvent($event)
                );
            }

            if ([] !== $temporaryListenersForEvent) {
                $this->listenersForEventsStorage[$eventName] = array_merge(...$temporaryListenersForEvent);
            }
        }

        return $this->listenersForEventsStorage[$eventName];
    }

    private function iterableToArray(iterable $iterable): array
    {
        if ($iterable instanceof \Traversable) {
            $iterable = iterator_to_array($iterable);
        }

        return $iterable;
    }
}

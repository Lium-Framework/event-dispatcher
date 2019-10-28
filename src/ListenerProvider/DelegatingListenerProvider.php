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
    private $runtimeStorage;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     */
    public function __construct(iterable $subListenerProviders)
    {
        $this->subListenerProviders = $this->iterableToArray($subListenerProviders);
        $this->runtimeStorage = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);

        if (!isset($this->runtimeStorage[$eventName])) {
            $this->runtimeStorage[$eventName] = [];

            $listenersForCurrentEvent = [];
            foreach ($this->subListenerProviders as $subListenerProvider) {
                $listenersForCurrentEvent[] = $this->iterableToArray($subListenerProvider->getListenersForEvent($event));
            }

            // This check will not be necessary anymore in PHP 7.4
            if ([] !== $listenersForCurrentEvent) {
                $this->runtimeStorage[$eventName] = array_merge(...$listenersForCurrentEvent);
            }
        }

        return $this->runtimeStorage[$eventName];
    }

    private function iterableToArray(iterable $iterable): array
    {
        if ($iterable instanceof \Traversable) {
            $iterable = iterator_to_array($iterable);
        }

        return $iterable;
    }
}

<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This ListenerProvider delegated its responsibilities to sub listeners and store the results.
 */
final class DelegatorListenerProvider implements ListenerProviderInterface
{
    /** @var ListenerProviderInterface[] */
    private $subListenerProviders;

    /** @var array<string, array<callable>> */
    private $cachedListeners;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     */
    public function __construct(
        iterable $subListenerProviders
    ) {
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

        if (!isset($this->cachedListeners[$eventName])) {
            $this->cachedListeners[$eventName] = [];
            foreach ($this->subListenerProviders as $subListenerProvider) {
                $this->cachedListeners[$eventName][] = $subListenerProvider->getListenersForEvent($event);
            }

            $this->cachedListeners[$eventName] = array_merge(...$this->cachedListeners[$eventName]);
        }

        return $this->cachedListeners[$eventName];
    }
}

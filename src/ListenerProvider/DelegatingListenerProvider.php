<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This ListenerProvider delegates its responsibilities to other listeners providers.
 */
final class DelegatingListenerProvider implements ListenerProviderInterface
{
    /** @var iterable<ListenerProviderInterface> */
    private $subListenerProviders;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     */
    public function __construct(iterable $subListenerProviders)
    {
        $this->subListenerProviders = $subListenerProviders;
    }

    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->subListenerProviders as $subListenerProvider) {
            yield from $subListenerProvider->getListenersForEvent($event);
        }
    }
}

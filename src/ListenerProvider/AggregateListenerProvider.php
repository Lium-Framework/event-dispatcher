<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * An aggregate provider encapsulates multiple other providers and concatenates their responses.
 *
 * Be aware that any ordering of listeners in different sub-providers is ignored, and providers are
 * iterated in the order in which they were added. That is, all listeners from the first provider
 * added will be returned to the caller, then all listeners from the second provider, and so on.
 *
 * @see https://github.com/php-fig/event-dispatcher-util/blob/1.2.0/src/AggregateProvider.php
 */
final class AggregateListenerProvider implements ListenerProviderInterface
{
    /** @var iterable<ListenerProviderInterface> */
    private iterable $subListenerProviders;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     */
    public function __construct(iterable $subListenerProviders)
    {
        $this->subListenerProviders = $subListenerProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->subListenerProviders as $subListenerProvider) {
            yield from $subListenerProvider->getListenersForEvent($event);
        }
    }
}

<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This ListenerProvider delegates its responsibilities to other listeners providers.
 */
final class DelegatingListenerProvider implements ListenerProviderInterface
{
    /** @var array<ListenerProviderInterface> */
    private $subListenerProviders;

    /**
     * @param iterable<ListenerProviderInterface> $subListenerProviders
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function __construct(iterable $subListenerProviders)
    {
        $this->subListenerProviders = $subListenerProviders instanceof \Traversable
            ? iterator_to_array($subListenerProviders)
            : $subListenerProviders;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->subListenerProviders as $subListenerProvider) {
            yield from $subListenerProvider->getListenersForEvent($event);
        }
    }
}

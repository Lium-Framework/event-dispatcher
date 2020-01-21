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
        $this->subListenerProviders = $this->iterableToArray($subListenerProviders);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listenersForEvent = [];

        foreach ($this->subListenerProviders as $subListenerProvider) {
            $listenersForEvent[] = $this->iterableToArray($subListenerProvider->getListenersForEvent($event));
        }

        // This check will not be necessary anymore in PHP 7.4
        if (count($listenersForEvent) > 0) {
            $listenersForEvent = array_merge(...$listenersForEvent);
        }

        return $listenersForEvent;
    }

    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     */
    private function iterableToArray(iterable $iterable): array
    {
        if ($iterable instanceof \Traversable) {
            $iterable = iterator_to_array($iterable);
        }

        return $iterable;
    }
}

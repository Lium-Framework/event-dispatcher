<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider\Decorator;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This listener provider decorates an other one to store its results.
 */
final class RuntimeStorageListenerProvider implements ListenerProviderInterface
{
    /** @var ListenerProviderInterface */
    private $decoratedListenerProvider;

    /** @var array<string, callable[]> */
    private $store;

    public function __construct(ListenerProviderInterface $decoratedListenerProvider)
    {
        $this->decoratedListenerProvider = $decoratedListenerProvider;
        $this->store = [];
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);
        if (!isset($this->store[$eventName])) {
            $listenersForEvent = $this->decoratedListenerProvider->getListenersForEvent($event);

            $this->store[$eventName] = $listenersForEvent instanceof \Traversable
                ? iterator_to_array($listenersForEvent)
                : $listenersForEvent;
        }

        return $this->store[$eventName];
    }

    public function reset(): void
    {
        $this->store = [];
    }
}

<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider\Decorator;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This listener provider decorate an other to store its results.
 */
final class RuntimeStorageListenerProvider implements ListenerProviderInterface
{
    /** @var ListenerProviderInterface */
    private $parentListenerProvider;

    /** @var array<string, callable[]> */
    private $store;

    public function __construct(ListenerProviderInterface $parentListenerProvider)
    {
        $this->parentListenerProvider = $parentListenerProvider;
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
            $listenersForEvent = $this->parentListenerProvider->getListenersForEvent($event);

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

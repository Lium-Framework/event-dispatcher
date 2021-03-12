<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider\Decorator;

use Lium\EventDispatcher\ListenerProvider\ResettableListenerProviderInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Traversable;
use function get_class;

/**
 * This listener provider decorates an other one to store its results in memory.
 */
final class RuntimeStorageListenerProvider implements ResettableListenerProviderInterface
{
    private ListenerProviderInterface $decoratedListenerProvider;

    /** @var array<class-string, array<callable>> */
    private array $store;

    public function __construct(ListenerProviderInterface $decoratedListenerProvider)
    {
        $this->decoratedListenerProvider = $decoratedListenerProvider;
        $this->store = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        return $this->store[get_class($event)] ??= $this->doGetListenersForEvent($event);
    }

    public function reset(): void
    {
        $this->store = [];
    }

    /**
     * @return array<callable>
     */
    private function doGetListenersForEvent(object $event): array
    {
        /** @var iterable<callable> $listenersForEvent */
        $listenersForEvent = $this->decoratedListenerProvider->getListenersForEvent($event);

        return $listenersForEvent instanceof Traversable
            ? iterator_to_array($listenersForEvent)
            : $listenersForEvent;
    }
}

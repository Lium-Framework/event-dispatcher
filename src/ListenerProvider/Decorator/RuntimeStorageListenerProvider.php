<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider\Decorator;

use Lium\EventDispatcher\ListenerProvider\ResettableListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;
use function get_class;

/**
 * This listener provider decorates an other one to store its results in memory.
 */
final class RuntimeStorageListenerProvider implements ResettableListenerProvider
{
    /** @var ListenerProviderInterface */
    private $decoratedListenerProvider;

    /** @var array<string, array<callable>> */
    private $store;

    public function __construct(ListenerProviderInterface $decoratedListenerProvider)
    {
        $this->decoratedListenerProvider = $decoratedListenerProvider;
        $this->store = [];
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);
        if (isset($this->store[$eventName]) === false) {
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

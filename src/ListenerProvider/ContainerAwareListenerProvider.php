<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener\ContainerAwareListener;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class ContainerAwareListenerProvider implements ListenerProviderInterface
{
    /** @var array<ContainerAwareListener> */
    private array $listeners;

    /**
     * @param iterable<string> $parsableListeners
     */
    public function __construct(iterable $parsableListeners, ContainerInterface $container)
    {
        $this->listeners = [];
        foreach ($parsableListeners as $listener) {
            $this->listeners[] = new ContainerAwareListener($listener, $container);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $listener) {
            if ($listener->match($event) === false) {
                continue;
            }

            yield $listener;
        }
    }
}

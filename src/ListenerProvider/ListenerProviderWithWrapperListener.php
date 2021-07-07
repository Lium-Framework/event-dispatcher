<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener\ListenerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

abstract class ListenerProviderWithWrapperListener implements ListenerProviderInterface
{
    /** @var array<ListenerInterface> */
    protected array $listeners;

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

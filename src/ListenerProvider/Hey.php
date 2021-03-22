<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener\ContainerAwareListener;
use Psr\Container\ContainerInterface;

final class Hey extends ListenerProviderWithWrapperListener
{
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
}

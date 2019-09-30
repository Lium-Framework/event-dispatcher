<?php

namespace Helium\EventDispatcher\Test\ListenerProvider;

use Helium\EventDispatcher\ListenerProvider\DefaultListenerProvider;
use Helium\EventDispatcher\Test\Event\ImmutableEvent;
use PHPUnit\Framework\TestCase;

class DefaultListenerProviderTest extends TestCase
{
    public function test_get_listeners_for_event_match()
    {
        $listeners = [
            function (ImmutableEvent $event) {
            }
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $this->assertSame($listeners, $listenerProvider->getListenersForEvent(new ImmutableEvent('Value')));
    }

    public function test_get_listeners_for_event_with_listener_without_parameters()
    {
        $listeners = [
            function () {
            }
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $this->assertSame([], $listenerProvider->getListenersForEvent(new ImmutableEvent('Value')));
    }
}

<?php

namespace Helium\EventDispatcher\Test\ListenerProvider;

use Helium\EventDispatcher\ListenerProvider\ListenerProvider;
use Helium\EventDispatcher\Test\Event\ImmutableEvent;
use PHPUnit\Framework\TestCase;

class ListenerProviderTest extends TestCase
{
    public function test_get_listeners_for_event_match()
    {
        $listeners = [
            function (ImmutableEvent $event) {
            }
        ];

        $listenerProvider = new ListenerProvider($listeners);

        $this->assertSame($listeners, $listenerProvider->getListenersForEvent(new ImmutableEvent('Value')));
    }

    public function test_get_listeners_for_event_with_listener_without_parameters()
    {
        $listeners = [
            function () {
            }
        ];

        $listenerProvider = new ListenerProvider($listeners);

        $this->assertSame([], $listenerProvider->getListenersForEvent(new ImmutableEvent('Value')));
    }
}

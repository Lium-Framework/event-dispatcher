<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\Test\ListenerProvider;

use Helium\EventDispatcher\ListenerProvider\DefaultListenerProvider;
use PHPUnit\Framework\TestCase;

class DefaultListenerProviderTest extends TestCase
{
    public function test_get_listeners_for_event_match()
    {
        $listeners = [
            function (\stdClass $event) {
            }
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $this->assertSame($listeners, $listenerProvider->getListenersForEvent(new \stdClass));
    }

    public function test_get_listeners_for_event_with_listener_without_parameters()
    {
        $listeners = [
            function () {
            }
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $this->assertSame([], $listenerProvider->getListenersForEvent(new \stdClass));
    }
}

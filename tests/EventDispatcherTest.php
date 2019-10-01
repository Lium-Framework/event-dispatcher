<?php

namespace Helium\EventDispatcher\Test;

use Helium\EventDispatcher\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcherTest extends TestCase
{
    public function test_dispatch()
    {
        /** @var MockObject|ListenerProviderInterface $mockListenerProvider */
        $mockListenerProvider = $this
            ->getMockBuilder(ListenerProviderInterface::class)
            ->getMock();

        $listeners = [
            function (object $event) {
            }
        ];

        $mockListenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn($listeners);

        $eventDispatcher = new EventDispatcher($mockListenerProvider);

        $event = new class {};

        $this->assertSame($event, $eventDispatcher->dispatch($event));
    }
}

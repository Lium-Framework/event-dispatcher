<?php

namespace Helium\EventDispatcher\Test;

use Helium\EventDispatcher\EventDispatcher;
use Helium\EventDispatcher\StoppableEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcherTest extends TestCase
{
    /** @var ListenerProviderInterface|MockObject */
    private $listenerProvider;

    /** @var EventDispatcher */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->listenerProvider = $this
            ->getMockBuilder(ListenerProviderInterface::class)
            ->getMock();

        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }

    protected function tearDown(): void
    {
        $this->listenerProvider = null;
        $this->eventDispatcher = null;
    }

    public function test_dispatch()
    {
        $this->listenerProviderWillReturn([
            function (object $event) {
            }
        ]);

        $event = new class
        {
        };

        $this->assertSame($event, $this->eventDispatcher->dispatch($event));
    }

    public function test_anonymous_function_listener_is_called()
    {
        $this->listenerProviderWillReturn([
            function (object $event) {
                $event->isCalled = true;
            }
        ]);

        $event = new class
        {
            public $isCalled = false;
        };

        $this->eventDispatcher->dispatch($event);

        $this->assertTrue($event->isCalled);
    }

    public function test_invokable_listener_is_called()
    {
        $this->listenerProviderWillReturn([new TestInvokableListener()]);

        $event = new class
        {
            public $isCalled = false;
        };

        $this->eventDispatcher->dispatch($event);

        $this->assertTrue($event->isCalled);
    }

    public function test_propagation_is_stoppable()
    {
        $this->listenerProviderWillReturn([
            function (TestStoppableEventWithCount $event) {
                $event->increment();
                $event->stopPropagation();
            },
            function (TestStoppableEventWithCount $event) {
                $event->increment();
            }
        ]);

        $event = new TestStoppableEventWithCount();

        $this->eventDispatcher->dispatch($event);

        $this->assertEquals(1, $event->getCount());
    }

    public function test_no_listener_is_called_when_event_propagation_is_stopped_before_dispatch()
    {
        $this->listenerProviderWillReturn([
            function (TestStoppableEventWithCount $event) {
                $event->increment();
            }
        ]);

        $event = new TestStoppableEventWithCount();
        $event->stopPropagation();

        $this->eventDispatcher->dispatch($event);

        $this->assertEquals(0, $event->getCount());
    }

    private function listenerProviderWillReturn(array $listeners)
    {
        $this
            ->listenerProvider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn($listeners);
    }
}

class TestInvokableListener
{
    public function __invoke(object $event)
    {
        $event->isCalled = true;
    }
}

class TestStoppableEventWithCount implements StoppableEventInterface
{
    use StoppableEventTrait;

    private $count = 0;

    public function getCount()
    {
        return $this->count;
    }

    public function increment()
    {
        $this->count++;
    }
}

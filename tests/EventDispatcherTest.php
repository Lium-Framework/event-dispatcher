<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\Test;

use Helium\EventDispatcher\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @see https://www.php-fig.org/psr/psr-14/#dispatcher
 */
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

    /** @test */
    public function MUST_call_listeners_synchronously_in_the_order_they_are_returned_from_a_ListenerProvider()
    {
        $trace = [];

        $this->listenerProvider->method('getListenersForEvent')->willReturn([
            function (object $event) use (&$trace) {
                $trace[] = 'listener 1 called';
            },
            function (object $event) use (&$trace) {
                $trace[] = 'listener 2 called';
            },
            function (object $event) use (&$trace) {
                $trace[] = 'listener 3 called';
            }
        ]);

        $this->eventDispatcher->dispatch(new \stdClass);

        $this->assertEquals(
            ['listener 1 called', 'listener 2 called', 'listener 3 called'],
            $trace,
            'Listener are not called in order they are returned from the ListenerProvider'
        );
    }

    /** @test */
    public function MUST_return_the_same_Event_object_it_was_passed_after_it_is_done_invoking_Listeners()
    {
        $this->listenerProvider->method('getListenersForEvent')->willReturn([
            function (object $event) {
            },
            function (object $event) {
            },
        ]);

        $event = new \stdClass;

        $this->assertSame(
            $event,
            $this->eventDispatcher->dispatch($event),
            'the Event object returned by the Dispatcher is not the dispatched one'
        );
    }

    /** @test */
    public function MUST_call_isPropagationStopped_on_the_Stoppable_Event_before_each_Listener_has_been_called()
    {
        $trace = [];

        $this->listenerProvider->method('getListenersForEvent')->willReturn([
            function ($event) use (&$trace) {
                $trace[] = 'listener 1 called';
            },
            function ($event) use (&$trace) {
                $trace[] = 'listener 2 called';
            },
            function ($event) use (&$trace) {
                $trace[] = 'listener 3 called';
            }
        ]);

        $event = $this->createMock(StoppableEventInterface::class);
        $event->method('isPropagationStopped')->willReturnOnConsecutiveCalls(false, false, true);

        $this->assertSame($event, $this->eventDispatcher->dispatch($event));
        $this->assertEquals(
            ['listener 1 called', 'listener 2 called'],
            $trace,
            'isPropagationStopped has not been called before each listener'
        );
    }

    /** @test */
    public function An_Exception_or_Error_thrown_by_a_Listener_MUST_block_the_execution_of_any_further_Listeners()
    {
        $trace = [];

        $this->listenerProvider->method('getListenersForEvent')->willReturn([
            function ($event) use (&$trace) {
                $trace[] = 'listener 1 called';
            },
            function ($event) use (&$trace) {
                $trace[] = 'listener 2 called';
                throw new \Exception();
            },
            function ($event) use (&$trace) {
                $trace[] = 'listener 3 called';
            }
        ]);

        $event = new \stdClass;

        try {
            $this->eventDispatcher->dispatch($event);
        } catch (\Exception $e) {
            $this->assertEquals(
                ['listener 1 called', 'listener 2 called'],
                $trace,
                'an exception did not block the execution of further listeners'
            );
        }
    }

    /** @test */
    public function An_Exception_or_Error_thrown_by_a_Listener_MUST_be_allowed_to_propagate_back_up_to_the_Emitter()
    {
        $exception = new \Exception();

        $this->listenerProvider->method('getListenersForEvent')->willReturn([
            function ($event) use ($exception) {
                $trace[] = 'listener 2 called';
                throw $exception;
            }
        ]);

        $event = new \stdClass;

        try {
            $this->eventDispatcher->dispatch($event);
        } catch (\Exception $e) {
            $this->assertSame($exception, $e, 'the exception is not propagated back up to the the emitter');
        }
    }
}

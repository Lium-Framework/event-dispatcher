<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test;

use Exception;
use Lium\EventDispatcher\EventDispatcher;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use stdClass;

/**
 * @see https://www.php-fig.org/psr/psr-14/#dispatcher
 */

uses()->group('event-dispatcher');

beforeEach(function () {
    $this->listenerProvider = $this
        ->getMockBuilder(ListenerProviderInterface::class)
        ->getMock();

    $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
});

it('MUST call listeners synchronously in the order they are returned from a ListenerProvider', function () {
    $trace = [];

    $this->listenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn([
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

    $this->eventDispatcher->dispatch(new stdClass());

    expect($trace)->toBe(['listener 1 called', 'listener 2 called', 'listener 3 called']);
});

it('MUST return the same Event object it was passed after it is done invoking listeners', function () {
    $this->listenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn([
        function (object $event) {},
        function (object $event) {},
    ]);

    $event = new stdClass();

    $result = $this->eventDispatcher->dispatch($event);

    expect($result)->toBe($event);
});

it('MUST call isPropagationStopped on the Stoppable Event before each Listener has been called', function () {
    $trace = [];

    $this->listenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn([
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
    $event
        ->expects($this->exactly(3))
        ->method('isPropagationStopped')
        ->willReturnOnConsecutiveCalls(false, false, true);

    $result = $this->eventDispatcher->dispatch($event);

    expect($result)->toBe($event);
    expect($trace)->toBe(['listener 1 called', 'listener 2 called']);
});

it('MUST block the execution of any further Listeners when an Exception or Error is thrown by a Listener', function () {
    $trace = [];

    $this->listenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn([
        function ($event) use (&$trace) {
            $trace[] = 'listener 1 called';
        },
        function ($event) use (&$trace) {
            $trace[] = 'listener 2 called';

            throw new Exception();
        },
        function ($event) use (&$trace) {
            $trace[] = 'listener 3 called';
        }
    ]);

    $event = new stdClass();

    try {
        $this->eventDispatcher->dispatch($event);
    } catch (Exception $e) {
        expect($trace)->toBe(['listener 1 called', 'listener 2 called']);
    }
});

it('MUST be allowed to propagate back up to the Emitter An Exception or Error thrown by a Listener', function () {
    $exception = new Exception();

    $this->listenerProvider->expects($this->once())->method('getListenersForEvent')->willReturn([
        function ($event) use ($exception) {
            $trace[] = 'listener 2 called';

            throw $exception;
        }
    ]);

    $event = new stdClass();

    try {
        $this->eventDispatcher->dispatch($event);
    } catch (Exception $e) {
        expect($e)->toBe($exception);
    }
});

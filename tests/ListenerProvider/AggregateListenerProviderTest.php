<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test\ListenerProvider;

use Lium\EventDispatcher\ListenerProvider\AggregateListenerProvider;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @group aggregateListenerProvider
 * @group listenerProvider
 */
class AggregateListenerProviderTest extends TestCase
{
    public function test_multiple_providers() : void
    {
        $provider1 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (object $event) {};
                yield function (object $event) {};
            }
        };

        $provider2 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (object $event) {};
                yield function (object $event) {};
                yield function (object $event) {};
            }
        };

        $aggregateListenerProvider = new AggregateListenerProvider([
            $provider1,
            $provider2,
        ]);

        $event = new class {};

        $listeners = $aggregateListenerProvider->getListenersForEvent($event);

        self::assertCount(5, $listeners);
    }
}

<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\Test\ListenerProvider;

use Helium\EventDispatcher\ListenerProvider\DefaultListenerProvider;
use PHPUnit\Framework\TestCase;

class DefaultListenerProviderTest extends TestCase
{
    /** @test */
    public function MUST_treats_parent_types_identically_to_the_own_type_of_the_Event()
    {
        $listeners = [
            function (A $a) {
            },
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $result = $listenerProvider->getListenersForEvent(new B());

        $this->assertSame(
            $listeners,
            $result instanceof \Traversable ? iterator_to_array($result) : $result,
            'The DefaultListenerProvider did not treats parent types identically to the own Event\'t type'
        );
    }

    /** @test */
    public function each_Listeners_MUST_be_type_compatible_with_the_Event()
    {
        $listeners = [
            function (object $object) {
            },
            function (A $a) {
            },
            function (B $b) {
            },
            function (\stdClass $stdClass) {
            },
        ];

        $listenerProvider = new DefaultListenerProvider($listeners);

        $event = new B();
        $result = $listenerProvider->getListenersForEvent($event);

        foreach ($result as $listener) {
            $closure = \Closure::fromCallable($listener);
            $reflectionFunction = new \ReflectionFunction($closure);
            $type = $reflectionFunction->getParameters()[0]->getType()->getName();

            $this->assertTrue(
                $event instanceof $type || 'object' === $type,
                sprintf("The Event of type %s is not type-compatible with type %s", get_class($event), $type)
            );
        }
    }

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

class A
{
}

class B extends A
{
}

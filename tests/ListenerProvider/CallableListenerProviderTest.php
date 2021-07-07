<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test\ListenerProvider;

use Lium\EventDispatcher\Exception\Listener\ListenerWithoutParameterException;
use Lium\EventDispatcher\ListenerProvider\CallableListenerProvider;
use PHPUnit\Framework\TestCase;

class CallableListenerProviderTest extends TestCase
{
    /** @test */
    public function MUST_treats_parent_types_identically_to_the_own_type_of_the_Event(): void
    {
        $listeners = [
            function (A $a) {
            },
        ];

        $listenerProvider = new CallableListenerProvider($listeners);

        $result = $listenerProvider->getListenersForEvent(new B());

        $this->assertSame(
            $listeners,
            $result instanceof \Traversable ? iterator_to_array($result) : $result,
            'The CallableListenerProvider did not treats parent types identically to the own Event\'t type'
        );
    }

    /** @test */
    public function each_Listeners_MUST_be_type_compatible_with_the_Event(): void
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

        $listenerProvider = new CallableListenerProvider($listeners);

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

    public function test_get_listeners_for_event_match(): void
    {
        $listeners = [
            function (\stdClass $event) {
            }
        ];

        $listenerProvider = new CallableListenerProvider($listeners);

        $this->assertSame($listeners, $listenerProvider->getListenersForEvent(new \stdClass));
    }

    public function test_get_listeners_for_event_with_listener_without_parameters(): void
    {
        $listeners = [
            function () {
            }
        ];

        $this->expectExceptionObject(new ListenerWithoutParameterException());
        new CallableListenerProvider($listeners);
    }
}

class A
{
}

class B extends A
{
}

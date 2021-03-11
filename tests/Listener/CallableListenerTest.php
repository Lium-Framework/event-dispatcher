<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test\Listener;

use Lium\EventDispatcher\Listener\CallableListener;
use Lium\EventDispatcher\Test\Stub\ListenerWithStaticMethod;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

/**
 * @group listener
 */
final class CallableListenerTest extends TestCase
{
    public function listenerProvider(): iterable
    {
        yield ['is_object'];
        yield [function (object $event) {}];
        yield [new class {
            public function __invoke(object $event) {}
        }];
        yield [[new class {
            public function fun(object $event) {}
        }, 'fun']];
        yield [[ListenerWithStaticMethod::class, 'staticMethod']];

        yield [[], TypeError::class];
        yield ['ttttt', TypeError::class];
        yield [[ListenerWithStaticMethod::class, 'anotherNonExistingMethod'], TypeError::class];
    }

    /**
     * @dataProvider listenerProvider
     *
     * @psalm-var class-string<Throwable>|null $expectedException
     */
    public function testConstruct($callable, ?string $expectedException = null): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        } else {
            $this->expectNotToPerformAssertions();
        }

        new CallableListener($callable);
    }
}

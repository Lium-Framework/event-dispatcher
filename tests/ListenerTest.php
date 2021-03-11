<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test;

use Lium\EventDispatcher\Listener;
use Lium\EventDispatcher\Test\Stub\ListenerWithStaticMethod;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

final class ListenerTest extends TestCase
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
        }

        $listener = new Listener($callable);

        $this->assertInstanceOf(Listener::class, $listener);
    }
}

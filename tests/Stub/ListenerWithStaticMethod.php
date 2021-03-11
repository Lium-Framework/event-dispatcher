<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test\Stub;

final class ListenerWithStaticMethod
{
    public static function staticMethod(object $event): void
    {
    }
}

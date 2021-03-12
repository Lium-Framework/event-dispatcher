<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

interface ListenerInterface
{
    public function __invoke(object $event): void;
    public function match(object $event): bool;
}

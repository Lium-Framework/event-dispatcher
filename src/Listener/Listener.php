<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

interface Listener
{
    public function match(object $event): bool;
    public function __invoke(object $event): void;
    public function getPriority(): int;
}

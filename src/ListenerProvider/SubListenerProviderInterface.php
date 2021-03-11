<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

interface SubListenerProviderInterface extends ListenerProviderInterface
{
    public function supports(callable $listener): bool;
}

<?php

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

interface ResettableListenerProviderInterface extends ListenerProviderInterface
{
    public function reset(): void;
}

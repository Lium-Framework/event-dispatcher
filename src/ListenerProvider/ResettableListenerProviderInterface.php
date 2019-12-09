<?php

namespace Lium\EventDispatcher\ListenerProvider;

interface ResettableListenerProviderInterface
{
    public function reset(): void;
}

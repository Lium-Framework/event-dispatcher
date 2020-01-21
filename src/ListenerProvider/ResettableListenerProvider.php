<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Provides a way to reset a listener provider to its initial state.
 *
 * When calling the "reset()" method on a listener provider, it should be put back
 * to its initial state. This usually means clearing any internal buffers and forwarding
 * the call to internal dependencies. All properties of the object should be put
 * back to the same state it had when it was first ready to use.
 */
interface ResettableListenerProvider extends ListenerProviderInterface
{
    public function reset(): void;
}

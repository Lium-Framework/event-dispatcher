<?php

declare(strict_types=1);

namespace Lium\EventDispatcher;

/**
 * A trait to implement the StoppableEventInterface behavior.
 * An alternative could be to implement the method isPropagationStopped() with dynamic data from the event.
 */
trait StoppableEventBehavior
{
    private bool $propagationStopped = false;

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool True if the Event is complete and no further listeners
     *              should be called. False to continue calling listeners.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): self
    {
        $this->propagationStopped = true;

        return $this;
    }
}

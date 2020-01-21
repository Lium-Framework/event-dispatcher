<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test;

use Lium\EventDispatcher\StoppableEventBehavior;
use PHPUnit\Framework\TestCase;

class StoppableEventTraitTest extends TestCase
{
    public function test_propagation_is_not_stopped_at_initialization()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventBehavior::class);

        $this->assertFalse($stoppableEvent->isPropagationStopped());
    }

    public function test_propagation_is_stopped_after_call_stop_propagation_method()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventBehavior::class);
        $stoppableEvent->stopPropagation();

        $this->assertTrue($stoppableEvent->isPropagationStopped());
    }

    public function test_stop_propagation_method_return_self()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventBehavior::class);

        $this->assertSame($stoppableEvent, $stoppableEvent->stopPropagation());
    }
}

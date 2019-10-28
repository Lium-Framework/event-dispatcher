<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\Test;

use Helium\EventDispatcher\StoppableEventTrait;
use PHPUnit\Framework\TestCase;

class StoppableEventTraitTest extends TestCase
{
    public function test_propagation_is_not_stopped_at_initialization()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventTrait::class);

        $this->assertFalse($stoppableEvent->isPropagationStopped());
    }

    public function test_propagation_is_stopped_after_call_stop_propagation_method()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventTrait::class);
        $stoppableEvent->stopPropagation();

        $this->assertTrue($stoppableEvent->isPropagationStopped());
    }

    public function test_stop_propagation_method_return_self()
    {
        $stoppableEvent = $this->getObjectForTrait(StoppableEventTrait::class);

        $this->assertSame($stoppableEvent, $stoppableEvent->stopPropagation());
    }
}

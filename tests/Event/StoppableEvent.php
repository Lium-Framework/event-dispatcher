<?php

namespace Helium\EventDispatcher\Test\Event;

use Helium\EventDispatcher\StoppableEventTrait;
use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent implements StoppableEventInterface
{
    use StoppableEventTrait;
}

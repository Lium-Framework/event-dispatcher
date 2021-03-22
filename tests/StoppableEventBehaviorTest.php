<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Test;

use Lium\EventDispatcher\StoppableEventBehavior;

uses()->group('stoppable-event-behavior');

beforeEach(function () {
    $this->stoppableEvent = $this->getObjectForTrait(StoppableEventBehavior::class);
});

it('doesn\'t stop propagation at initialization', function () {
    expect($this->stoppableEvent->isPropagationStopped())->toBeFalse();
});

it('stops propagation after the call of "stopPropagation" method', function () {
    $this->stoppableEvent->stopPropagation();

    expect($this->stoppableEvent->isPropagationStopped())->toBeTrue();
});

it('should return self', function () {
    expect($this->stoppableEvent->stopPropagation())->toBe($this->stoppableEvent);
});

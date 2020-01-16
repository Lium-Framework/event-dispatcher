<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Exception;

final class InvalidListener extends \InvalidArgumentException
{
    /** @var callable */
    protected $listener;

    public function __construct(callable $listener, ?string $reasonPhrase = null)
    {
        $message = sprintf(
            'Listener invalid. %s',
            $reasonPhrase ?? 'The listener must have only one parameter which the type is the event class it listen to or the scalar type "object".'
        );

        parent::__construct($message);

        $this->listener = $listener;
    }

    public function getListener(): callable
    {
        return $this->listener;
    }
}

<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Exception;

final class InvalidListener extends \InvalidArgumentException
{
    /** @var callable */
    protected $listener;

    public function __construct(callable $listener, string $reasonPhrase = '')
    {
        $message = sprintf(
            'Listener invalid. %s',
            $reasonPhrase
        );

        parent::__construct($message);

        $this->listener = $listener;
    }

    public function getListener(): callable
    {
        return $this->listener;
    }
}

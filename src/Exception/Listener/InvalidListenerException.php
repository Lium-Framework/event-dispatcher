<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Exception\Listener;

use InvalidArgumentException;
use Throwable;

abstract class InvalidListenerException extends InvalidArgumentException
{
    private const DEFAULT_REASON_PHRASE = 'The listener must have only one parameter. This parameter type must be the event class it listen to, the scalar type "object" or not defined.';

    public function __construct(?string $reasonPhrase = null, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Listener invalid. %s',
            $reasonPhrase ?? self::DEFAULT_REASON_PHRASE,
        );

        parent::__construct($message, $code, $previous);
    }
}

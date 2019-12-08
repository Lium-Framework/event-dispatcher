<?php

namespace Lium\EventDispatcher\Exception;

class InvalidListenerException extends \InvalidArgumentException
{
    /** @var callable */
    protected $listener;

    /** @var string */
    protected $reasonPhrase;

    public function __construct(callable $listener, string $reasonPhrase = '', int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf(
            'Listener invalid. %s',
            $reasonPhrase
        );

        parent::__construct($message, $code, $previous);

        $this->listener = $listener;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getListener(): callable
    {
        return $this->listener;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}

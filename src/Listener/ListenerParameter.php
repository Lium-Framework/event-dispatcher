<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

/**
 * @internal
 */
final class ListenerParameter
{
    private const ALLOWED_TYPES = [
        null,
        'object',
    ];

    /** @var string|null */
    private $typeName;

    public function __construct(\ReflectionParameter $reflectionParameter)
    {
        $type = $reflectionParameter->getType();
        if ($type === null) {
            $this->typeName = null;
            return;
        }

        $this->typeName = $type->getName();
        if ($this->typeName !== 'object' && $reflectionParameter->getClass() === null) {
            throw new \InvalidArgumentException();
        }
    }

    public function matchEvent(object $event): bool
    {
        if (\in_array($this->typeName, self::ALLOWED_TYPES)) {
            return true;
        }

        return $event instanceof $this->typeName;
    }
}

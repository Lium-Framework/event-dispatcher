<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use InvalidArgumentException;
use Lium\EventDispatcher\Exception\Listener\InvalidListenerParameterTypeException;
use Lium\EventDispatcher\Exception\Listener\ListenerWithoutParameterException;
use Lium\EventDispatcher\Exception\Listener\UnsupportedListenerParameterTypeException;
use Lium\EventDispatcher\Utils\Reflection\ReflectionType;
use ReflectionFunctionAbstract;
use function in_array;

abstract class ReflectionParameterListener implements ListenerInterface
{
    public const SCALAR_TYPE_OBJECT = 'object';

    /**
     * @var string|null The type of the listener's first parameter. It should be either null if the type is not defined,
     *                  the scalar type object or an event FQCN.
     */
    protected ?string $type;

    /**
     * @var bool True when the listener's first parameter type is not defined or is the scalar type object.
     */
    protected bool $eventAlwaysMatches;

    public function __construct(ReflectionFunctionAbstract $reflectionFunction)
    {
        $this->type = $this->extractListenerFirstParameterType($reflectionFunction);
    }

    public function match(object $event): bool
    {
        return $this->eventAlwaysMatches || $event instanceof $this->type;
    }

    private function extractListenerFirstParameterType(ReflectionFunctionAbstract $reflectionFunction): ?string
    {
        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new ListenerWithoutParameterException();
        }

        try {
            $type = ReflectionType::findReflectionNamedType($reflectionParameter);
        } catch (InvalidArgumentException $exception) {
            throw new UnsupportedListenerParameterTypeException();
        }

        $this->eventAlwaysMatches = in_array($type, [null, self::SCALAR_TYPE_OBJECT], true);
        if ($this->eventAlwaysMatches === false && $reflectionParameter->getClass() === null) {
            throw new InvalidListenerParameterTypeException();
        }

        return $type;
    }
}

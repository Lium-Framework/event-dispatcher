<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use InvalidArgumentException;
use Lium\EventDispatcher\Exception\Listener\InvalidListenerParameterTypeException;
use Lium\EventDispatcher\Exception\Listener\ListenerWithoutParameterException;
use Lium\EventDispatcher\Exception\Listener\NotSupportedListenerParameterTypeException;
use Lium\EventDispatcher\Utils\Reflection\ReflectionType;
use ReflectionFunctionAbstract;

abstract class ReflectionParameterListener implements ListenerInterface
{
    private const SCALAR_TYPE_OBJECT = 'object';

    private ?string $type;

    public function __construct(ReflectionFunctionAbstract $reflectionFunction)
    {
        $this->type = $this->extractListenerFirstParameterType($reflectionFunction);
    }

    public function match(object $event): bool
    {
        if ($this->type === null || $this->type === self::SCALAR_TYPE_OBJECT) {
            return true;
        }

        return $event instanceof $this->type;
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
            throw new NotSupportedListenerParameterTypeException();
        }

        if ($type !== self::SCALAR_TYPE_OBJECT && $reflectionParameter->getClass() === null) {
            throw new InvalidListenerParameterTypeException();
        }

        return $type;
    }
}

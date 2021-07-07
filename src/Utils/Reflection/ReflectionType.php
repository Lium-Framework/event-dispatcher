<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Utils\Reflection;

use InvalidArgumentException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @internal
 */
final class ReflectionType
{
    /**
     * @throws InvalidArgumentException
     */
    public static function findReflectionNamedType(ReflectionParameter $reflectionParameter): ?string
    {
        $reflectionType = $reflectionParameter->getType();
        if ($reflectionType === null) {
            return null;
        }

        if ($reflectionType instanceof ReflectionNamedType === false) {
            throw new InvalidArgumentException(sprintf(
                'The given %s doesn\'t have a %s',
                ReflectionParameter::class,
                ReflectionNamedType::class,
            ));
        }

        return $reflectionType->getName();
    }
}

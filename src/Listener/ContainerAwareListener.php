<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use InvalidArgumentException;
use Lium\EventDispatcher\Exception\InvalidListener;
use Lium\EventDispatcher\Utils\Reflection\ReflectionType;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use function method_exists;
use function strpos;

final class ContainerAwareListener implements Listener
{
    private ContainerInterface $container;
    private int $priority;
    private string $service;
    private string $method;
    private ?string $type;
    private bool $alwaysMatching;

    public function __construct(string $listenerString, ContainerInterface $container, int $priority = 0)
    {
        $this->container = $container;
        $this->priority = $priority;

        [$this->service, $this->method] = $this->parseListenerString($listenerString);

        $this->type = $this->extractListenerFirstParameterType($this->service, $this->method);
        $this->alwaysMatching = in_array($this->type, [null, 'object'], true);
    }

    public function __invoke(object $event): void
    {
        if ($this->container->has($this->service) === false) {
            throw new \RuntimeException(sprintf(
                'The given %s doesn\'t contains a service identified with key %s,',
                ContainerInterface::class,
                $this->service,
            ));
        }

        /** @var object $service */
        $service = $this->container->get($this->service);

        /** @psalm-suppress MixedMethodCall */
        $service->{$this->method}($event);
    }

    public function match(object $event): bool
    {
        return $this->alwaysMatching || $event instanceof $this->type;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return array{0: class-string, 1: string}
     */
    private function parseListenerString(string $listenerString): array
    {
        if (strpos($listenerString, '::')) {
            [$service, $method] = explode('::', $listenerString);
        } else {
            $service = $listenerString;
            $method = '__invoke';
        }

        if (method_exists($service, $method) === false) {
            throw new InvalidArgumentException();
        }

        /**
         * @var class-string $service
         * @var string $method The service's method that will be the listener
         */
        return [$service, $method];
    }

    /**
     * @param class-string $service
     */
    private function extractListenerFirstParameterType(string $service, string $method): ?string
    {
        $reflectionMethod = new ReflectionMethod($service, $method);
        $reflectionParameter = $reflectionMethod->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener();
        }

        try {
            $type = ReflectionType::findReflectionNamedType($reflectionParameter);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidListener();
        }

        if ($type !== 'object' && $reflectionParameter->getClass() === null) {
            throw new InvalidListener();
        }

        return $type;
    }
}

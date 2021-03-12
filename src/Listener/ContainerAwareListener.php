<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use function method_exists;
use function strpos;

final class ContainerAwareListener extends ReflectionParameterListener
{
    private ContainerInterface $container;
    private string $service;
    private string $method;

    public function __construct(string $listenerString, ContainerInterface $container)
    {
        $this->container = $container;

        [$this->service, $this->method] = $this->parseListenerString($listenerString);

        parent::__construct(new ReflectionMethod($this->service, $this->method));
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

    /**
     * @return array{0: class-string, 1: string}
     */
    private function parseListenerString(string $listenerString): array
    {
        /**
         * @var class-string $service
         * @var string $method The service's method that will be the concret listener
         */
        [$service, $method] = strpos($listenerString, '::') !== false
            ? explode('::', $listenerString)
            : [$listenerString, '__invoke'];

        if (method_exists($service, $method) === false) {
            throw new InvalidArgumentException();
        }

        return [$service, $method];
    }
}

<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\Listener;

use Psr\Container\ContainerInterface;

final class ContainerAwareListener implements Listener
{
    private string $toParse;
    private ContainerInterface $container;
    private int $priority;

    public function __construct(string $toParse, ContainerInterface $container)
    {
        $this->toParse = $toParse;
        $this->container = $container;
        $this->priority = 0;
    }

    public function match(object $event): bool
    {
        return true;
    }

    public function __invoke(object $event): void
    {
        /** @var callable $service */
        $service = $this->container->get($this->toParse);

        $service($event);
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}

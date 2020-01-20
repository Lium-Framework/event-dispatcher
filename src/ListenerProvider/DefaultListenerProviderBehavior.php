<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Exception\InvalidListener;

trait DefaultListenerProviderBehavior
{
    /** @var array<callable> */
    private $listeners;

    /** @var array<string>|null */
    private $listenerParameterMap;

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->listenerParameterMap === null) {
            // Prepare the map
            $this->listenerParameterMap = array_map(
                [$this, 'getListenerUniqueParameterType'],
                $this->listeners
            );
        }

        return array_filter($this->listeners, function (int $key) use ($event) {
            return $event instanceof $this->listenerParameterMap[$key] || $this->listenerParameterMap[$key] === 'object';
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getListenerUniqueParameterType(callable $listener): string
    {
        $closure = \Closure::fromCallable($listener);
        $reflectionFunction = new \ReflectionFunction($closure);

        $reflectionParameter = $reflectionFunction->getParameters()[0] ?? null;
        if ($reflectionParameter === null) {
            throw new InvalidListener($listener);
        }

        $type = $reflectionParameter->getType();
        if ($type === null) {
            throw new InvalidListener($listener);
        }

        $typeName = $type->getName();
        if ($typeName !== 'object' && $reflectionParameter->getClass() === null) {
            throw new InvalidListener($listener);
        }

        return $typeName;
    }
}

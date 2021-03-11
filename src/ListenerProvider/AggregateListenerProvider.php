<?php

declare(strict_types=1);

namespace Lium\EventDispatcher\ListenerProvider;

use Lium\EventDispatcher\Listener;
use Lium\EventDispatcher\Test\Stub\ListenerWithStaticMethod;
use Psr\EventDispatcher\ListenerProviderInterface;

final class AggregateListenerProvider implements ListenerProviderInterface
{
    /** @var iterable<Listener> */
    private iterable $listeners;

    /** @var iterable<SubListenerProviderInterface> */
    private iterable $listenerProviders;

    /**
     * @param iterable<callable> $listeners
     * @param iterable<string> $parsers
     */
    public function autre(iterable $listeners, iterable $serviceListeners)
    {
       /* foreach ($listeners as $listener) {
            foreach ($parsers as $parser) {
                if ($parser->supports($listener)) {
                    $parser->getListenerProvider()->getListenerForEvent();
                }
            }
        };*/
    }

    public function __autreConstruct(iterable $autreListenerProvider)
    {
        $this->autreListenerProvider = $autreListenerProvider;
    }

    /**
     * @param iterable<callable|class-string> $listeners
     * @param iterable<SubListenerProviderInterface> $listenerProviders
     */
    public function __construct(iterable $listeners, iterable $listenerProviders)
    {
        /*new AggregateListenerProvider([
            function(object $event) {}, // CallableListenerProvider / CallableListener
            [ListenerWithStaticMethod::class, 'staticMethod'], // CallableListenerProvider / CallableListener
            fn($event) => 0, // CallableListenerProvider / CallableListener
            'ListenerWithNonStaticMethod::nonStaticMethod', // ContainerAwareListenerProvider / ContainerServiceListener
            \ListenerInvokable::class, // ContainerAwareListenerProvider / ContainerServiceListener
        ], [
            [function(ContainerAwareListenerProvider $provider, mixed $listener): bool { return is_string($listener) && parse($listener) && $provider->has($parsedListener); }, new ContainerAwareListenerProvider($container), ServiceContainerListener::class],
            [function(ListenerProviderInterface $provider, mixed $listener): bool { return is_callable($listener); }, new CallableListenerProvider(), CallableListener::class],
        ]);*/

        $this->listeners = [];
        foreach ($listeners as $listener) {
            foreach ($listenerProviders as $listenerProvider) {
                if (true === $listenerProvider[0]($listenerProvider[1], $listener)) {
                    $this->listeners[] = new $listenerProvider[1]->createListener($listener);
                    continue 2;
                }
            }
            throw new \Exception('No provider found for whatever');
        }

        $this->listenerProviders = $listenerProviders;
    }

    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $listener) {
            foreach ($this->listenerProviders as $subListenerProvider) {
                // $listener === new Listener([ListenerWithStaticMethod::class, 'staticMethod'])
                // public function supports()
                if ($subListenerProvider->supports($listener->getCallable()) === false) {
                    continue;
                }

                yield from $subListenerProvider->getListenersForEvent($event);
            }
        }
    }

    private function getListenerProviderForListener(callable $listener): SubListenerProviderInterface
    {
        $matching = [];
        foreach ($this->listenerProviders as $subListenerProvider) {
            if ($subListenerProvider->supports($listener)) {
                $matching[] = $subListenerProvider;
            }
        }

        $matchingCount = \count($matching);

        if ($matchingCount === 0) {
            throw new \LogicException();
        }

        if ($matchingCount > 1) {
            throw new \LogicException();
        }

        return reset($matching);
    }
}

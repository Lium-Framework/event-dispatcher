<?php

declare(strict_types=1);

namespace Helium\EventDispatcher\ListenerProvider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * This ListenerProvider decorate an other one in order to register sub listener providers to handle an event given by its name 
 */
class RegistrationListenerProvider implements ListenerProviderInterface
{
    /** @var ListenerProviderInterface */
    private $baseListenerProvider;

    /** @var array */
    private $subListenerProvidersMap;

    /** @var array */
    private $cachedListeners;

    public function __construct(ListenerProviderInterface $baseListenerProvider, array $subListenerProvidersMap)
    {
        $this->baseListenerProvider = $baseListenerProvider;
        $this->subListenerProvidersMap = $subListenerProvidersMap;
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventName = get_class($event);

        if (!isset($this->cachedListeners[$eventName])) {
            $subListenerProvider = $this->subListenerProvidersMap[$eventName] ?? null;
            if ($subListenerProvider && $subListenerProvider instanceof ListenerProviderInterface) {
                return $this->cachedListeners[$eventName] = $subListenerProvider->getListenersForEvent($event);
            }

            $this->cachedListeners[$eventName] = $this->baseListenerProvider->getListenersForEvent($event);
        }

        return $this->cachedListeners[$eventName];
    }
}

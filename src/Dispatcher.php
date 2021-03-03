<?php

namespace Spartan\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Dispatcher Event
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class Dispatcher implements EventDispatcherInterface
{
    protected ListenerProviderInterface $provider;

    public function __construct(ListenerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param object $event
     *
     * @return object
     */
    public function dispatch(object $event): object
    {
        /** @var StoppableEventInterface $event */
        $isStoppable = $event instanceof StoppableEventInterface;

        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            if ($isStoppable && $event->isPropagationStopped()) {
                break;
            }

            if (is_string($listener)) {
                (new $listener())($event);
            } else {
                $listener($event);
            }
        }

        return $event;
    }
}

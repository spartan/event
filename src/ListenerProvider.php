<?php

namespace Spartan\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * ListenerProvider Event
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @param object $event
     *
     * @return iterable|mixed[]
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (method_exists($event, 'listeners')) {
            return $event->listeners();
        }

        return [];
    }
}

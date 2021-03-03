<?php

if (!function_exists('dispatch')) {

    /**
     * Emit an event
     *
     * @param mixed $event
     *
     * @return object
     * @throws ReflectionException
     * @throws \Spartan\Service\Exception\ContainerException
     * @throws \Spartan\Service\Exception\NotFoundException
     */
    function dispatch($event)
    {
        return container()->get('event')->dispatch($event);
    }
}

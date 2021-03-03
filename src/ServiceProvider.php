<?php

namespace Spartan\Event;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Spartan\Service\Container;
use Spartan\Service\Definition\ProviderInterface;
use Spartan\Service\Pipeline;

/**
 * ServiceProvider Event
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class ServiceProvider implements ProviderInterface
{
    /**
     * @param ContainerInterface $container
     * @param Pipeline           $handler
     *
     * @return ContainerInterface
     */
    public function process(ContainerInterface $container, Pipeline $handler): ContainerInterface
    {
        /** @var Container $container */
        return $handler->handle(
            $container->withBindings($this->singletons(), $this->prototypes())
        );
    }

    /**
     * @return mixed[]
     */
    public function singletons(): array
    {
        return [
            'event'                         => EventDispatcherInterface::class,
            EventDispatcherInterface::class => function ($c) {
                return new \Spartan\Event\Dispatcher(
                    new \Spartan\Event\ListenerProvider()
                );
            },
        ];
    }

    /**
     * @return mixed[]
     */
    public function prototypes(): array
    {
        return [];
    }
}

<?php

namespace Spartan\Event;

use Spartan\Event\Definition\EventInterface;

/**
 * Simple Event
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class SimpleEvent implements EventInterface
{
    /**
     * @var mixed[]
     */
    protected array $payload = [];

    /**
     * Event constructor.
     *
     * @param mixed $payload
     *
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public function __construct($payload)
    {
        $this->withPayload($payload);
    }

    /**
     * {@inheritDoc}
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * {@inheritDoc}
     */
    public function withPayload($payload): self
    {
        $this->payload = (array)$payload + $this->payload;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listeners(): array
    {
        return [];
    }
}

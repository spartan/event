<?php

namespace Spartan\Event\Definition;

/**
 * EventInterface
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
interface EventInterface
{
    /**
     * Get event payload
     *
     * @return mixed[]
     */
    public function payload(): array;

    /**
     * Update event payload
     *
     * @param mixed[] $payload
     *
     * @return $this
     */
    public function withPayload(array $payload): self;

    /**
     * List of listeners attached to the current event
     *
     * @return mixed[]
     */
    public function listeners(): array;
}

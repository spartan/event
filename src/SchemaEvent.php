<?php

namespace Spartan\Event;

use Spartan\Event\Definition\EventInterface;

/**
 * Schema Event
 *
 * @package Spartan\Event
 * @author  Iulian N. <iulian@spartanphp.com>
 * @license LICENSE MIT
 */
class SchemaEvent implements EventInterface
{
    const SCHEMA = [
        '$schema' => 'https://json-schema.org/draft/2019-09/schema',
        '$id'     => 'https://example.com/json',
        'type'    => 'object',
    ];

    /**
     * @var mixed[]
     */
    protected array $payload = [];

    /**
     * JsonEvent constructor.
     *
     * @param mixed $payload
     *
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public function __construct($payload)
    {
        $this->validate($payload);

        $this->withPayload($payload);
    }

    /**
     * @param mixed $payload
     *
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public function validate($payload): void
    {
        $context = new \Swaggest\JsonSchema\Context();
        $schema  = \Swaggest\JsonSchema\Schema::import(
            json_decode((string)json_encode(static::SCHEMA)),
            $context
        );
        $schema->in(json_decode((string)json_encode($payload)));
    }

    /**
     * @return mixed[]
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     *
     * @return $this
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
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

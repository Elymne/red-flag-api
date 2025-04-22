<?php

namespace Domain\Models;

/**
 * Data structure about the zone where a Person lives.
 */
readonly class Zone
{
    public function __construct(
        /** @var string */
        public string $ID,

        /** @var string */
        public string $name,
    ) {}

    public function copyWith(?string $ID = null, ?string $name = null): self
    {
        return new self(
            $ID ?? $this->ID,
            $name ?? $this->name
        );
    }
}

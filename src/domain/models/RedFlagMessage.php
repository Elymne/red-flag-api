<?php

namespace Domain\Models;

use Ramsey\Uuid\UuidInterface;

readonly class RedFlagMessage
{
    public function __construct(
        public UuidInterface $id,
        public string $value,

        public int $createdAt,
        public int $updatedAt,
    ) {}
}

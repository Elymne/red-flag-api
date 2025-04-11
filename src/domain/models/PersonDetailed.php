<?php

namespace Domain\Models;

use Ramsey\Uuid\UuidInterface;

readonly class PersonDetailed
{
    public function __construct(
        public UuidInterface $id,
        public string $firstName,
        public string $lastName,

        public Zone $zone,
        public array $messages,
        public array $links,

        public int $createdAt,
        public int $updatedAt,
    ) {}
}

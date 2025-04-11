<?php

namespace Domain\Models;

use Ramsey\Uuid\UuidInterface;

readonly class Person
{
    public function __construct(
        public UuidInterface $id,
        public string $firstName,
        public string $lastName,
        public Zone $zone,

        public int $createdAt,
        public int|null $updatedAt,
    ) {}
}

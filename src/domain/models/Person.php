<?php

namespace Domain\Models;

readonly class Person
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public Zone $zone,

        public int $createdAt,
        public int|null $updatedAt = null,
    ) {}
}

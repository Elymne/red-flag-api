<?php

namespace Domain\Models;

readonly class Person
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public City $city,

        public int $createdAt,
        public int $updatedAt,
    ) {}
}

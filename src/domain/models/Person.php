<?php

namespace Domain\Models;

readonly class Person
{
    public function __construct(
        public string $id,
        public string $first_name,
        public string $last_name,
        public City $city,

        public int $created_at,
        public int $updated_at,
    ) {}
}

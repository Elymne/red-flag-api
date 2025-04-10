<?php

namespace Domain\Models;

readonly class Person
{
    /**
     * @param City[] $cities
     */
    public function __construct(
        public string $id,
        public string $first_name,
        public string $last_name,
        public array $cities,

        public int $created_at,
        public int $updated_at,
    ) {}
}

<?php

namespace Domain;

readonly class RedFlag
{
    public function __construct(
        public string $id,
        public string $first_name,
        public string $last_name,

        public int $created_at,
        public int $updated_at,
    ) {}
}

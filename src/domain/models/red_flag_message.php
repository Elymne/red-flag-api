<?php

namespace Domain;

readonly class RedFlagMessage
{
    public function __construct(
        public string $id,
        public string $message,

        public int $created_at,
        public int $updated_at,
    ) {}
}

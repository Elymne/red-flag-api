<?php

namespace Domain\Models;

readonly class RedFlagLink
{
    public function __construct(
        public string $id,
        public string $value,

        public int $createdAt,
        public int $updatedAt,
    ) {}
}

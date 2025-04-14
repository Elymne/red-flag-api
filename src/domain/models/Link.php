<?php

namespace Domain\Models;

readonly class Link
{
    public function __construct(
        public string $id,
        public string $value,
        public int $createdAt,
        public int|null $updatedAt = null,
    ) {}
}

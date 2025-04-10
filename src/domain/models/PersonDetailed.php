<?php

namespace Domain\Models;

readonly class PersonDetailed
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,

        public array $messages,
        public array $links,
        public array $cities,

        public int $createdAt,
        public int $updatedAt,
    ) {}
}

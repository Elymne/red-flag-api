<?php

namespace Domain\Models;

use Ramsey\Uuid\UuidInterface;

readonly class PersonDetailed extends Person
{
    public function __construct(
        UuidInterface $id,
        string $firstName,
        string $lastName,
        Zone $zone,
        int $createdAt,
        int|null $updatedAt,

        public array $messages,
        public array $links,
    ) {
        parent::__construct($id, $firstName, $lastName, $zone, $createdAt, $updatedAt);
    }
}

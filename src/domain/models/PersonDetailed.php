<?php

namespace Domain\Models;

readonly class PersonDetailed extends Person
{
    public function __construct(
        string $id,
        string $firstName,
        string $lastName,
        string $jobName,
        Zone $zone,
        int $createdAt,

        public array $messages,
        public array $links,

        int|null $updatedAt = null,
    ) {
        parent::__construct($id, $firstName, $lastName, $jobName, $zone, $createdAt, $updatedAt);
    }
}

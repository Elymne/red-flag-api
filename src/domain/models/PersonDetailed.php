<?php

namespace Domain\Models;

readonly class PersonDetailed extends Person
{
    public function __construct(
        string $id,
        string $firstName,
        string $lastName,
        string $jobName,
        int $birthday,

        string|null $portrait, // * From Wiki API.
        public string|null $description, // * From Wiki API.

        Zone $zone,
        int $createdAt,
        int|null $updatedAt = null,

        // * Articles list from website (trusted sources).
        /** @var Link[] */
        public array $links,
    ) {
        parent::__construct($id, $firstName, $lastName, $birthday, $portrait, $jobName, $zone, $createdAt, $updatedAt);
    }
}

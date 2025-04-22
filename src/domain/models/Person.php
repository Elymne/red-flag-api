<?php

namespace Domain\Models;

use DateTime;

/**
 * Data Structure of a person from Database or Remote Datasource (Wikipedia for example).
 */
readonly class Person
{
    public function __construct(
        /** @var string */
        public string $ID,

        /** @var string */
        public string $firstname,

        /** @var string */
        public string $lastname,

        /** @var string */
        public string|null $pseudonym,

        /** @var int Can come from Remote Data */
        public int $birthDate,

        /** @var Zone Can come from Remote Data */
        public Zone $zone,

        /** @var Activity|null The current activity. Can be unknwown. */
        public Activity|null $activity,

        /** @var Company|null The current company. Can be unknwown. */
        public Company|null $company,

        /** @var Link[] */
        public array $links,

        /** @var int */
        public int $createdAt,

        /** @var string|null Come Remote Data. */
        public string|null $portrait = null,

        /** @var string|null Com Remote Data. */
        public string|null $description = null,
    ) {}

    public function copyWith(
        ?string $ID = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $pseudonym = null,
        ?int $birthDate = null,
        ?Zone $zone = null,
        ?array $activity = null,
        ?array $company = null,
        ?array $links = null,
        ?string $portrait = null,
        ?string $description = null,
        ?int $createdAt = null,
    ): self {
        return new self(
            $ID ?? $this->ID,
            $firstname ?? $this->firstname,
            $lastname ?? $this->lastname,
            $pseudonym ?? $this->pseudonym,
            $birthDate ?? $this->birthDate,
            $zone ?? $this->zone,
            $activity ?? $this->activity,
            $company ?? $this->company,
            $links ?? $this->links,
            $portrait ?? $this->portrait,
            $description ?? $this->description,
            $createdAt ?? $this->createdAt,
        );
    }
}

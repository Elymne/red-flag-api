<?php

namespace Domain\Models;

readonly class Person
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public int $birthday,

        public ?string $portrait, // * Nullable Nullable From Wiki API.

        public string $jobName,
        public Zone $zone,

        public int $createdAt,
        public int|null $updatedAt = null,
    ) {}

    public function copyWith(
        ?string $id = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?int $birthday = null,
        ?string $portrait = null,
        ?string $jobName = null,
        ?Zone $zone = null,
        ?int $createdAt = null,
        ?int $updatedAt = null
    ): self {
        return new self(
            id: $id ?? $this->id,
            firstName: $firstName ?? $this->firstName,
            lastName: $lastName ?? $this->lastName,
            birthday: $birthday ?? $this->birthday,
            portrait: $portrait ?? $this->portrait,
            jobName: $jobName ?? $this->jobName,
            zone: $zone ?? $this->zone,
            createdAt: $createdAt ?? $this->createdAt,
            updatedAt: $updatedAt ?? $this->updatedAt,
        );
    }
}

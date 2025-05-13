<?php

namespace Domain\Models;

readonly class PersonDetailed
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public int $birthday,

        public string $jobName,
        public Zone $zone,

        public int $createdAt,

        // * Articles list from website.
        /** @var Link[] */
        public array $links,

        // * Optionnals
        public int|null $updatedAt = null,
        public ?string $portrait = null, // * Nullable because From Wiki API.
        public ?string $description = null, // * Nullable because From Wiki API.
    ) {}

    /**
     * Simple clone function.
     */
    public function copyWith(
        ?string $id = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $jobName = null,
        ?int $birthday = null,
        ?string $portrait = null,
        ?string $description = null,
        ?Zone $zone = null,
        ?int $createdAt = null,
        ?int $updatedAt = null,
        ?array $links = null
    ): self {
        return new self(
            id: $id ?? $this->id,
            firstName: $firstName ?? $this->firstName,
            lastName: $lastName ?? $this->lastName,
            birthday: $birthday ?? $this->birthday,
            portrait: $portrait ?? $this->portrait,
            description: $description ?? $this->description,
            jobName: $jobName ?? $this->jobName,
            zone: $zone ?? $this->zone,
            createdAt: $createdAt ?? $this->createdAt,
            updatedAt: $updatedAt ?? $this->updatedAt,
            links: $links ?? $this->links
        );
    }
}

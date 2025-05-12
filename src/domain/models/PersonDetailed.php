<?php

namespace Domain\Models;

readonly class PersonDetailed
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public int $birthday,

        public ?string $portrait, // * Nullable Nullable From Wiki API.
        public ?string $description, // * Nullable because From Wiki API.

        public string $jobName,
        public Zone $zone,

        public int $createdAt,
        public int|null $updatedAt = null,

        // * Articles list from website.
        /** @var Link[] */
        public array $links,
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
            $id ?? $this->id,
            $firstName ?? $this->firstName,
            $lastName ?? $this->lastName,
            $jobName ?? $this->jobName,
            $birthday ?? $this->birthday,
            $portrait ?? $this->portrait,
            $description ?? $this->description,
            $zone ?? $this->zone,
            $createdAt ?? $this->createdAt,
            $updatedAt ?? $this->updatedAt,
            $links ?? $this->links
        );
    }
}

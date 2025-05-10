<?php

namespace Domain\Models;

/**
 * Data structure that represent person data fetched from external sources that I don't own.
 * The data that my backend cannot manage (and that I don't want to) is portrait image url and description.
 */
readonly class PersonRemoteData
{
    public function __construct(
        public string|null $portrait,
        public string|null $description,
    ) {}
}

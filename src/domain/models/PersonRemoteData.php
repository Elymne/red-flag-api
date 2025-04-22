<?php

namespace Domain\Models;

/**
 * Data structure of Remote information about a person.
 */
readonly class PersonRemoteData
{
    public function __construct(
        /** @var string */
        public string|null $portrait = null,

        /** @var string */
        public string|null $description = null,

        /** @var string */
        public string|null $birthday = null,

        /** @var string */
        public string|null $activities = null,

        /** @var string */
        public string|null $firstname = null,

        /** @var string */
        public string|null $lastname = null,
    ) {}
}

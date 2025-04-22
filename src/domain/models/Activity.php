<?php

namespace Domain\Models;

/**
 * Data structure about Link/Article about a person.
 */
readonly class Activity
{
    public function __construct(
        /** @var string */
        public string $ID,

        /** @var string */
        public string $name,
    ) {}
}

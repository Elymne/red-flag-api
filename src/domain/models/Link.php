<?php

namespace Domain\Models;

/**
 * Data structure about Link/Article about a person.
 */
readonly class Link
{
    public function __construct(
        /** @var string */
        public string $ID,

        /** @var string */
        public string $source,

        /** @var int */
        public int $createdAt,
    ) {}
}

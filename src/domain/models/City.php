<?php

namespace Domain\Models;

/**
 * Can be an article, Youtube video.
 * All theses links shoudl be certified from safe sources.
 */
readonly class City
{
    public function __construct(
        public string $id, // The code from the api I use.
        public string $name,
    ) {}
}

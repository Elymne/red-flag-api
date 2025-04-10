<?php

namespace Domain\Models;

/**
 * Can be an article, Youtube video.
 * All theses links shoudl be certified from safe sources.
 */
readonly class RedFlagLink
{
    public function __construct(
        public string $id,
        public string $link,

        public int $created_at,
        public int $updated_at,
    ) {}
}

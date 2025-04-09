<?php

namespace Domain;

readonly class RedFlagLink
{
    public function __construct(
        public string $id,
        public string $link,

        public int $created_at,
        public int $updated_at,
    ) {}
}

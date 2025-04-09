<?php

namespace Domain;

readonly class RedFlagDetailed
{
    /**
     * @param RedFlagMessage[] $messages
     * @param RedFlagLink[] $links
     * @param City[] $cities
     */
    public function __construct(
        public string $id,
        public string $first_name,
        public string $last_name,

        public array $messages,
        public array $links,
        public array $cities,

        public int $created_at,
        public int $updated_at,
    ) {}
}

<?php

namespace Domain;

/**
 * Messages on the current red flags leave by users.
 * I'll try to filter rudes words but to be honest, I don't give a fuck.
 */
readonly class RedFlagMessage
{
    public function __construct(
        public string $id,
        public string $message,

        public int $created_at,
        public int $updated_at,
    ) {}
}

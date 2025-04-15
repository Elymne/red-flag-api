<?php

namespace Domain\Models;

readonly class Zone
{
    public function __construct(
        public string $id, // The code from the api I use.
        public string $name,
    ) {}
}

<?php

namespace Domain\Models;

readonly class City
{
    public function __construct(
        public string $id, // The code from the api I use.
        public string $name,
    ) {}
}

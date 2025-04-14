<?php

namespace Domain\Usecases;

readonly class InsertPersonParams
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $zoneId,
    ) {}
}

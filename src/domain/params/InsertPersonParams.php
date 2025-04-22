<?php

namespace Domain\Usecases;

readonly class InsertPersonParams
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public int $birthDate,
        public string $zoneID,
        public string|null $activityID,
        public string|null $companyID,
    ) {}
}

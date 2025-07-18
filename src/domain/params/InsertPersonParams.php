<?php

namespace Domain\Usecases;

readonly class InsertPersonParams
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public int $birthDate,
        public string $zoneID,
        public string $activityID,
        public string $companyID,
    ) {}
}

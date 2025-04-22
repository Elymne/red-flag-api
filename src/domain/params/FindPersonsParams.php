<?php

namespace Domain\Usecases;

readonly class FindPersonsParams
{
    public function __construct(
        public string|null $firstname = null,
        public string|null $lastname = null,
        public int|null $birthDate = null,
        public string|null $activityID = null,
        public string|null $companyID = null,
        public string|null $zoneID = null,
    ) {}
}

<?php

namespace Domain\Usecases;

readonly class InsertPersonParams
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public string $jobname,
        public string $zoneID,
    ) {}
}

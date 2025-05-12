<?php

namespace Domain\Usecases;

readonly class InsertPersonParams
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public int $birthday,
        public string $jobname,
        public string $zoneID,
    ) {}
}

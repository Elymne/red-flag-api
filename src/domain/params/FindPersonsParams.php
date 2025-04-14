<?php

namespace Domain\Usecases;

readonly class FindPersonsParams
{
    public function __construct(
        public string|null $firstname,
        public string|null $lastname,
        public string|null $zonename,
        public string|null $jobname,
    ) {}
}

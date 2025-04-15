<?php

namespace Domain\Usecases;

readonly class FindPersonsParams
{
    public function __construct(
        public string|null $firstname = null,
        public string|null $lastname = null,
        public string|null $jobname = null,
        public string|null $zonename = null,
    ) {}
}

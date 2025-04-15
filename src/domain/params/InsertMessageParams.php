<?php

namespace Domain\Usecases;

readonly class InsertMessageParams
{
    public function __construct(
        public string $personID,
        public string $message,
    ) {}
}

<?php

namespace Domain\Usecases;

readonly class InsertMessageParams
{
    public function __construct(
        public string $personId,
        public string $message,
    ) {}
}

<?php

namespace Domain\Usecases;

use Ramsey\Uuid\UuidInterface;

readonly class InsertMessageParams
{
    public function __construct(
        public UuidInterface $personId,
        public string $message,
    ) {}
}

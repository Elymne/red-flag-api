<?php

namespace Domain\Usecases;

use Ramsey\Uuid\UuidInterface;

readonly class InsertLinkParams
{
    public function __construct(
        public UuidInterface $personId,
        public string $link,
    ) {}
}

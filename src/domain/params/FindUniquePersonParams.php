<?php

namespace Domain\Usecases;

use Ramsey\Uuid\UuidInterface;

readonly class FindUniquePersonParams
{
    public function __construct(
        public UuidInterface $id,
    ) {}
}

<?php

namespace Infra\Uuid;

use Domain\Repositories\UuidRepository;
use Ramsey\Uuid\Uuid;

class RamseyUuid implements UuidRepository
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function generateBytes(): string
    {
        return Uuid::uuid4()->getBytes();
    }

    public function toBytes(string $uuid): string
    {
        return Uuid::fromString($uuid)->getBytes();
    }
}

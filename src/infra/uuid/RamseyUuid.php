<?php

namespace Infra\Uuid;

use Domain\Repositories\UuidRepository;
use Ramsey\Uuid\Uuid;

class RamseyUuid implements UuidRepository
{
    public function generate(): string
    {
        return Uuid::uuid4();
    }
}

<?php

namespace Domain\Usecases;

readonly class FindZonesParams
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}

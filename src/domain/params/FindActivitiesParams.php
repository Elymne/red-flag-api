<?php

namespace Domain\Usecases;

readonly class FindActivitiesParams
{
    public function __construct(
        public string $name
    ) {}
}

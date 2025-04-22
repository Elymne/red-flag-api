<?php

namespace Domain\Usecases;

readonly class FindActivityByIDParams
{
    public function __construct(
        public string $ID,
    ) {}
}

<?php

namespace Domain\Usecases;

readonly class InsertLinkParams
{
    public function __construct(
        public string $personId,
        public string $link,
    ) {}
}

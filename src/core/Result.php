<?php

declare(strict_types=1);

namespace Core;

readonly class Result
{
    public function __construct(
        public int $code, // http code response or 1 or 0.
        public mixed $data // the data that client will receive.
    ) {
        // TODO : Logger ici pour étudier chaque entrée.
    }
}

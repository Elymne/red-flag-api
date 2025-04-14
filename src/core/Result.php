<?php

namespace Core;

readonly class Result
{
    public function __construct(
        public string $code, // http code response or 1 or 0.
        public mixed $data // the data that client will receive.
    ) {
        // TODO : Logger ici pour étudier chaque entrée.
    }
}

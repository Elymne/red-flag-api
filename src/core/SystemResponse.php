<?php

declare(strict_types=1);

namespace Core;

class SystemResponse
{
    public function __construct(
        public bool $success,
        public string|null $message = null,
    ) {}
}

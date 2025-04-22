<?php

namespace Domain\Gateways;

use Core\LogData;

interface LoggerGateway
{
    public function info(LogData $logData): void;
    public function warning(LogData $logData): void;
    public function error(LogData $logData): void;
    public function critical(LogData $logData): void;
}

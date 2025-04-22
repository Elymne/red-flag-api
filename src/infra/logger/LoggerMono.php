<?php

namespace Infra\Logger;

use Core\LogData;
use Domain\Gateways\LoggerGateway;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerMono implements LoggerGateway
{
    public Logger $mainLogger;
    private const ROTATING_TIME = 31;

    public function __construct()
    {
        $this->mainLogger = new Logger("main");
        $this->mainLogger->pushHandler(new StreamHandler("php://stdout", \Monolog\Level::Info));
        $this->mainLogger->pushHandler(new StreamHandler("php://stdout", \Monolog\Level::Warning));
        $this->mainLogger->pushHandler(new StreamHandler("php://stdout", \Monolog\Level::Error));
        $this->mainLogger->pushHandler(new StreamHandler("php://stdout", \Monolog\Level::Critical));
        $this->mainLogger->pushHandler(new RotatingFileHandler(ROOT_PATH . "/logs/main.log", self::ROTATING_TIME, \Monolog\Level::Info));
        $this->mainLogger->pushHandler(new RotatingFileHandler(ROOT_PATH . "/logs/main.log", self::ROTATING_TIME, \Monolog\Level::Warning));
        $this->mainLogger->pushHandler(new RotatingFileHandler(ROOT_PATH . "/logs/main.log", self::ROTATING_TIME, \Monolog\Level::Error));
        $this->mainLogger->pushHandler(new RotatingFileHandler(ROOT_PATH . "/logs/main.log", self::ROTATING_TIME, \Monolog\Level::Critical));
    }

    public function info(LogData $logData): void
    {
        $this->mainLogger->info(
            $logData->message,
            [
                "file" => $logData->file,
                "line" => $logData->line,
                "trace" => $logData->trace,
            ]
        );
    }

    public function warning(LogData $logData): void
    {
        $this->mainLogger->warning(
            $logData->message,
            [
                "file" => $logData->file,
                "line" => $logData->line,
                "trace" => $logData->trace,
            ]
        );
    }

    public function error(LogData $logData): void
    {
        $this->mainLogger->error(
            $logData->message,
            [
                "file" => $logData->file,
                "line" => $logData->line,
                "trace" => $logData->trace,
            ]
        );
    }

    public function critical(LogData $logData): void
    {
        $this->mainLogger->critical(
            $logData->message,
            [
                "file" => $logData->file,
                "line" => $logData->line,
                "trace" => $logData->trace,
            ]
        );
    }
}

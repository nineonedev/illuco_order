<?php

namespace Framework\Support;

class Logger
{
    protected string $logPath;

    public function __construct(?string $logPath = null)
    {
        $this->logPath = $logPath ?? storage_path('logs/app.log');
    }

    protected function write(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '';
        $log = "[$date] $level: $message";

        if ($contextStr) {
            $log .= PHP_EOL . $contextStr;
        }

        $log .= PHP_EOL . str_repeat('-', 80) . PHP_EOL;

        file_put_contents($this->logPath, $log, FILE_APPEND);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('DEBUG', $message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->write(strtoupper($level), $message, $context);
    }
}

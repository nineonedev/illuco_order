<?php 

namespace Framework\Support; 

class Logger 
{
    protected string $logPath; 

    public function __construct(string $logPath)
    {
        $this->logPath = $logPath; 
    }

    public function error(string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''; 
        $log = "[$date] ERROR: {$message} {$contextStr}" . PHP_EOL;
        file_put_contents($this->logPath, $log, FILE_APPEND);
    }
}
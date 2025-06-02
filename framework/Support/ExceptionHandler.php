<?php

namespace Framework\Support;

use Framework\Configurations\ExceptionConfigurator;
use Throwable;
use Framework\Support\Exceptions\HttpException;
use Framework\Support\Exceptions\ValidationException;

class ExceptionHandler
{
    protected Logger $logger;
    protected bool $debug = false;
    protected ?ExceptionConfigurator $configurator = null;

    public function __construct(bool $debug = false)
    {
        $this->logger = new Logger();
        $this->debug = $debug;
    }

    public function setConfigurator(ExceptionConfigurator $configurator): void
    {
        $this->configurator = $configurator; 
    }

    public function handle(Throwable $e): void
    {
        if ($this->configurator && $this->configurator->shouldIgnore($e)) {
            return; 
        }

        if ($this->configurator) {
            foreach ($this->configurator->getReportables() as $callback) {
                if ($callback($e) === false) {
                    return;
                }
            }
        }

        $code = $e instanceof HttpException ? $e->getStatusCode() : 500;
        $this->logger->error($e->getMessage(), $this->details($e, $code));

        if ($this->configurator) {
            foreach ($this->configurator->getRenderables() as $callback) {
                $result = $callback($e); 
                if ($result !== null) {
                    echo $result; 
                    return; 
                }
            }
        }

        if (php_sapi_name() === 'cli') {
            $this->handleCli($e);
            return;
        }

        if ($this->isJsonRequest()) {
            $this->handleJson($e, $code);
            return;
        }

        $this->handleHtml($e, $code);
    }

    protected function details(Throwable $e, int $code): array
    {
        return [
            'code' => $code,
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
    }

    protected function handleCli(Throwable $e): void
    {
        echo "[Exception] " . $e->getMessage() . PHP_EOL;

        if ($this->debug) {
            echo $this->details($e, $e->getCode())['trace'] . PHP_EOL;
        }
    }


    protected function handleHtml(Throwable $e, int $code): void
    {
        http_response_code($code);

        echo '<h1>에러 발생</h1>';
        echo '<p>' . escape($e->getMessage()) . '</p>';

        if ($this->debug) {
            $details = $this->details($e, $code);
            
            echo '<h3>예외 정보 (디버그)</h3>';
            echo '<ul>';
            echo '<li><strong>Exception:</strong> ' . escape($details['exception']) . '</li>';
            echo '<li><strong>File:</strong> ' . escape($details['file']) . '</li>';
            echo '<li><strong>Line:</strong> ' . escape((string)$details['line']) . '</li>';
            echo '</ul>';

            echo '<h3>Stack Trace</h3>';
            echo '<pre>' . escape($details['trace']) . '</pre>';
        }
    }

    protected function handleJson(Throwable $e, int $code): void
    {
        $response = [
            'error' => true,
            'message' => $e->getMessage(),
            'code' => $code,
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
        }

        if ($this->debug) {
            $details = $this->details($e, $code);
            $response['debug'] = [
                'exception' => $details['exception'],
                'file' => $details['file'],
                'line' => $details['line'],
                'trace' => explode("\n", $details['trace']),
            ];
        }

        echo json($response);
    }

    protected function isJsonRequest(): bool
    {
        if (php_sapi_name() === 'cli') {
            return false;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return stripos($accept, 'application/json') !== false;
    }
}

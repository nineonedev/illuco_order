<?php

namespace Framework\Support\Exceptions;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Http\ApiResponse;
use Framework\Http\Response;
use Framework\Support\Exceptions\Http\HttpException;
use Framework\Support\Logger;
use Throwable;

class ExceptionHandler
{
    protected static $metaData = [];
    protected bool $debug = false;
    protected Logger $logger;
    protected ?ExceptionConfigurator $configurator = null;

    public function __construct(bool $debug = false)
    {
        $this->logger = new Logger();
        $this->debug = $debug;
    }

    public function setDebug(bool $debug): void
    {
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
                try {
                    $result = $callback($e);
                    if ($result !== null) {
                        echo $result;
                        return;
                    }
                } catch (Throwable $ex) {
                    http_response_code(500);
                    echo '<h1>렌더러 오류</h1>';
                    echo '<pre>' . $ex->getMessage() . '</pre>';
                    return;
                }
            }
        }

        // CLI
        if (php_sapi_name() === 'cli') {
            if ($e instanceof RenderableException) {
                $e->renderForCli();
            } else {
                $this->handleCli($e);
            }
            return;
        }

        // JSON
        if (request()->expectsJson()) {
            if ($e instanceof RenderableException) {
                $e->renderJson($this->debug)->send();
            } else {
                $this->handleJson($e, $code)->send();
            }
            return;
        }

        // HTML
        if ($e instanceof RenderableException) {
            $e->renderHtml()->send();
        } else {
            $this->handleHtml($e, $code)->send();
        }
    }

    protected function details(Throwable $e, int $code): array
    {
        return [
            'code'      => $code,
            'message'   => $e->getMessage(),
            'exception' => get_class($e),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => $e->getTraceAsString(),
        ];
    }

    protected function handleCli(Throwable $e): void
    {
        $code = $e instanceof HttpException ? $e->getStatusCode() : 500;
        $this->logger->error($e->getMessage(), $this->details($e, $code));

        echo "[Exception] {$e->getMessage()}" . PHP_EOL;

        if ($this->debug) {
            echo $this->details($e, $code)['trace'] . PHP_EOL;
        }
    }

    protected function handleHtml(Throwable $e, int $code): Response
    {
        http_response_code($code);
        
        $viewPath = config('view.errors') . '.' . $code;
        $fallbackView = config('view.fallbacks.error');
        $template = view()->exists($viewPath) ? $viewPath : $fallbackView; 


        if (!config('app.debug')) {
            return response()
                ->setStatusCode($code)
                ->view($template);
        }

        $template = config('view.debug');
        $linesBefore = 10;
        $linesAfter = 10;
        $codeLines = [];

        $file = $e->getFile();
        $line = $e->getLine();

        if (is_file($file)) {
            $fileLines = file($file);
            $start = max($line - $linesBefore - 1, 0);
            $end = min($line + $linesAfter - 1, count($fileLines) - 1);

            for ($i = $start; $i <= $end; $i++) {
                $codeLines[] = [
                    'number'    => $i + 1,
                    'code'      => rtrim($fileLines[$i]),
                    'highlight' => ($i + 1 === $line),
                ];
            }
        }

        return response()
            ->setStatusCode($code)
            ->view($template, [
                'message'   => $e->getMessage(),
                'file'      => $file,
                'line'      => $line,
                'trace'     => $e->getTraceAsString(),
                'exception' => get_class($e),
                'snippet'   => $codeLines,
            ]);
    }

    protected function handleJson(Throwable $e, int $code): Response
    {
        $debug = null;

        if ($this->debug) {
            $details = $this->details($e, $code);
            $debug = [
                'exception' => $details['exception'],
                'file'      => $details['file'],
                'line'      => $details['line'],
                'trace'     => explode("\n", $details['trace']),
            ];
        }

        return ApiResponse::fail($e->getMessage(), [], $code, $debug);
    }
}

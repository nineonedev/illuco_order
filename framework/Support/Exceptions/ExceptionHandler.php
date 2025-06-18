<?php

namespace Framework\Support\Exceptions;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Console\Output\Output;
use Framework\Console\UI\Table;
use Framework\Core\Application;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Response;
use Framework\Support\Logger;
use Throwable;

class ExceptionHandler
{
    protected static $metaData = [];
    protected bool $debug = true;
    protected Logger $logger;
    protected ?ExceptionConfigurator $configurator = null;

    public function __construct(bool $debug = true)
    {
        $this->logger = new Logger(Application::getInstance()->loggerPath());
        $this->debug = $debug;
    }

    public static function setMeta(string $key, $value): void
    {
        static::$metaData[$key] = $value;
    }

    public static function clearMeta(): void
    {
        static::$metaData = [];
    }

    public static function getMeta(string $key = null)
    {
        return $key === null ? static::$metaData : (static::$metaData[$key] ?? null);
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
        $code = $e instanceof BaseException ? $e->getCode() : 500;

        if (php_sapi_name() === 'cli') {
            $this->handleCli($e);
            return;
        }

        $this->logger->error($e->getMessage(), array_merge(
            $this->details($e, $code),
            ['meta' => static::$metaData]
        ));

        static::clearMeta();

        // 사용자 정의 콜백
        if ($this->configurator) {
            if ($this->configurator->shouldIgnore($e)) return;

            foreach ($this->configurator->getReportables() as $callback) {
                if ($callback($e) === false) return;
            }

            foreach ($this->configurator->getRenderables() as $callback) {
                try {
                    $result = $callback($e);
                    if ($result instanceof ResponseInterface) {
                        $result->send();
                        return;
                    } elseif ($result !== null) {
                        echo $result;
                        return;
                    }
                } catch (Throwable $ex) {
                    http_response_code(500);
                    $this->simpleError('렌더러 오류', $ex->getMessage(), $ex, $this->debug);
                    return;
                }
            }
        }

        if (!Application::getInstance()->isBooted()) {
            $this->renderBeforeBooting($e, $code);
            return;
        }


        if (function_exists('request') && request()->expectsJson()) {
            $this->handleJson($e, $code);
            return;
        }

        $this->handleHtml($e, $code);
    }

    protected function renderBeforeBooting(Throwable $e, int $code): void
    {
        $fallback = config('path.debug');
        if (is_file($fallback)) {
            $this->renderView($fallback, $e, $code);
        } else {
            $this->simpleError("Application Error [{$code}]", $e->getMessage(), $e, true);
        }
    }

    protected function handleHtml(Throwable $e, int $code): void
    {
        http_response_code($code);

        while (ob_get_level() > 0) ob_end_clean();

        if ($e instanceof BaseException) {
            $e->renderHtmlResponse()->send();
            return; 
        }
        
        $view = config('path.error');

        if ($this->debug) {
            $view = config('path.debug');
        }

        if (!is_file($view)) {
            $this->renderBeforeBooting($e, $code);
        }
        
        ob_start();
        $this->renderView($view, $e, $code);
        $content = ob_get_clean();

        Response::html($content, $code)->send();
    }

    protected function renderView(string $absolutePath, Throwable $e, int $code): void
    {
        try {
            $details = $this->details($e, $code);
            $meta = static::$metaData;
            $debug = $this->debug;

            extract(compact('e', 'code', 'details', 'meta', 'debug'));
            include $absolutePath;
        } catch (Throwable $ex) {
            $this->simpleError("렌더링 중 중첩 예외 발생", $ex->getMessage(), $ex, $this->debug);
        }
    }

    protected function handleCli(Throwable $e): void
    {
        $output = new Output();

        $code = $e instanceof BaseException ? $e->getCode() : 500;
        $details = $this->details($e, $code);

        $output->newLine();
        $output->error("[{$details['exception']}]");
        $output->writeln("  " . $details['message']);
        $output->writeln("  at " . $details['file'] . ' : ' . $details['line']);
        $output->newLine();

        if ($this->debug) {
            $table = new Table(['#', 'Call']);
            $lines = explode("\n", $details['trace']);
            foreach ($lines as $i => $line) {
                $table->addRow([$i + 1, $line]);
            }

            $table->render();
            $output->newLine();
        }
    }

    protected function handleJson(Throwable $e, int $code): void
    {
        while (ob_get_level() > 0) ob_end_clean();

        if ($e instanceof BaseException) {
            $e->renderJsonResponse()->send();
            return;
        }

        $isProd = Application::getInstance()->isProduction();
        
        $payload = [
            'success' => false,
            'message' => $isProd ? lang('validation.unexpected') : $e->getMessage(),
            'errors' => [],
        ];

        if ($this->debug) {
            $details = $this->details($e, $code);
            $payload['debug'] = [
                'exception' => $details['exception'],
                'file' => $details['file'],
                'line' => $details['line'],
                'trace' => explode("\n", $details['trace']),
            ];
        }

        if (!empty(static::$metaData)) {
            $payload['meta'] = static::$metaData;
        }

        if (!empty($payload['errors'])) {
            $first = reset($payload['errors']);
            if (is_array($first)) {
                $payload['message'] = reset($first);
            } else {
                $payload['message'] = $first;
            }
        }

        Response::json($payload, $code)->send();
    }

    protected function details(Throwable $e, int $code): array
    {
        $isProd = Application::getInstance()->isProduction();
        $message = $isProd ? lang('validation.unexpected') : $e->getMessage();
        
        return [
            'code' => $code,
            'message' => $message,
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
    }

    protected function simpleError(string $title, string $message, Throwable $e = null, bool $debug = false): void
    {
        echo $this->simpleErrorString($title, $message, $e, $debug);
    }

    protected function simpleErrorString(string $title, string $message, Throwable $e = null, bool $debug = false): string
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title></head><body>';
        $html .= "<h1>" . htmlspecialchars($title) . "</h1>";
        $html .= "<pre>" . htmlspecialchars($message) . "</pre>";
        if ($debug && $e) {
            $html .= "<pre>at " . $e->getFile() . " : " . $e->getLine() . "</pre>";
            $html .= "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        $html .= '</body></html>';
        return $html;
    }
}
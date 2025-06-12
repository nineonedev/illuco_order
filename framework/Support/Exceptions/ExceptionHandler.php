<?php

namespace Framework\Support\Exceptions;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Core\Application;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Response;
use Framework\Http\Responses\HtmlResponse;
use Framework\Http\Responses\JsonResponse;
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

        if (php_sapi_name() === 'cli') {
            $this->handleCli($e);
            return;
        }

        if (function_exists('request') && request()->expectsJson()) {
            $this->handleJson($e, $code)->send();
            return;
        }

        $this->handleHtml($e, $code)->send();
    }

    protected function renderBeforeBooting(Throwable $e, int $code): void
    {
        $fallback = base_path('supports/debug.php');
        if (is_file($fallback)) {
            $this->renderView($fallback, $e, $code);
        } else {
            $this->simpleError("Application Error [{$code}]", $e->getMessage(), $e, true);
        }
    }

    protected function handleHtml(Throwable $e, int $code): HtmlResponse
    {
        http_response_code($code);

        while (ob_get_level() > 0) ob_end_clean();

        if ($e instanceof BaseException) {
            $html = $e->renderHtml();
            return Response::html($html, $code);
        }
        
        if (Application::getInstance()->isBooted()) {
            $view = config('path.error');
            if ($this->debug && is_file(config('path.debug'))) {
                $view = config('path.debug');
            }
        } else {
            $view = base_path('supports/error.php');
            if ($this->debug && is_file(base_path('supports/debug.php'))) {
                $view = base_path('supports/debug.php');
            }
        }

        ob_start();
        $this->renderView($view, $e, $code);
        $content = ob_get_clean();

        return Response::html($content, $code);
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
        $code = $e instanceof BaseException ? $e->getCode() : 500;
        $this->logger->error($e->getMessage(), $this->details($e, $code));

        echo "[Exception] {$e->getMessage()}" . PHP_EOL;
        if ($this->debug) echo $this->details($e, $code)['trace'] . PHP_EOL;
    }

    protected function handleJson(Throwable $e, int $code): JsonResponse
    {
        if ($e instanceof BaseException) {
            return Response::json($e->renderJson(), $code);
        }
        
        $payload = [
            'success' => false,
            'message' => $e->getMessage(),
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

        return Response::json($payload, $code);
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
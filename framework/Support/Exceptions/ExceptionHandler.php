<?php

namespace Framework\Support\Exceptions;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Core\Application;
use Framework\Http\Contracts\ResponseInterface;
use Framework\Http\Response;
use Framework\Http\Responses\HtmlResponse;
use Framework\Http\Responses\JsonResponse;
use Framework\Support\Exceptions\Http\HttpException;
use Framework\Support\Logger;
use Throwable;

class ExceptionHandler
{
    protected static $metaData = [];
    protected bool $debug = true;
    protected Logger $logger;
    protected ?ExceptionConfigurator $configurator = null;
    protected string $baseViewPath;
    protected string $errorFallbackView = 'supports/error.php';
    protected string $successFallbackView = 'supports/success.php';
    protected string $debugFallbackView = 'supports/debug.php';

    public function __construct(bool $debug = true)
    {
        $this->logger = new Logger(Application::getInstance()->loggerPath());
        $this->debug = $debug;
        $this->baseViewPath = rtrim(Application::getInstance()->viewPath(), '/');
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
        if ($key === null) return static::$metaData;
        return static::$metaData[$key] ?? null;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function setConfigurator(ExceptionConfigurator $configurator): void
    {
        $this->configurator = $configurator;
    }

    /**
     * 등록된 예외를 환경에 따라 적절하게 처리 및 출력합니다.
     *
     * 처리 흐름은 다음과 같습니다:
     * 1. 예외를 로깅하고, reportable / renderable 콜백 실행
     * 2. 애플리케이션이 부트되지 않은 경우 fallback 뷰 또는 단순 HTML 출력
     * 3. CLI 환경에서는 콘솔에 예외 정보 출력
     * 4. JSON 요청인 경우 JSON 응답 반환 (디버그 정보 포함 가능)
     * 5. 그 외 HTML 요청이면 debug 또는 error fallback 뷰 렌더링
     *
     * @param Throwable $e 처리할 예외 객체
     * @return void
     */    
    public function handle(Throwable $e): void
    {
        $code = $e instanceof HttpException ? $e->getStatusCode() : 500;

        $logDetails = array_merge(
            $this->details($e, $code),
            ['meta' => static::$metaData]
        );

        $this->logger->error($e->getMessage(), $logDetails);

        static::clearMeta();

        // ignore/reportable/renderable 처리
        if ($this->configurator && $this->configurator->shouldIgnore($e)) return;
        if ($this->configurator) {
            foreach ($this->configurator->getReportables() as $callback) {
                if ($callback($e) === false) return;
            }
            foreach ($this->configurator->getRenderables() as $callback) {
                try {
                    $result = $callback($e);
                    if ($result !== null) {
                        if ($result instanceof ResponseInterface) {
                            $result->send();
                        } else {
                            echo $result;
                        }
                        return;
                    }
                } catch (Throwable $ex) {
                    http_response_code(500);
                    $this->simpleError('렌더러 오류', $ex->getMessage());
                    return;
                }
            }
        }

        // 부트 전이면 renderBeforeBooting
        if (!Application::getInstance()->isBooted()) {
            $this->renderBeforeBooting($e, $code);
            return;
        }

        // CLI
        if (php_sapi_name() === 'cli') {
            $this->handleCli($e);
            return;
        }

        // JSON
        if (function_exists('request') && request()->expectsJson()) {
            $this->handleJson($e, $code)->send();
            return;
        }

        // HTML (supports/error.php, supports/debug.php 사용)
        $this->handleHtml($e, $code)->send();
    }

    protected function renderBeforeBooting(Throwable $e, int $code): void
    {
        if ($this->hasView($this->debugFallbackView)) {
            $this->renderView($this->debugFallbackView, $e, $code);
            return;
        }
        $this->simpleError("Application Error [{$code}]", $e->getMessage(), $e, true);
    }

    protected function handleHtml(Throwable $e, int $code): HtmlResponse
    {
        http_response_code($code);

        $view = $this->errorFallbackView;
        if ($this->debug && $this->hasView($this->debugFallbackView)) {
            $view = $this->debugFallbackView;
        }

        ob_start();
        $this->renderView($view, $e, $code);
        $content = ob_get_clean();
        return Response::html($content, $code);
    }

    protected function hasView(string $relativePath): bool
    {
        return is_file($this->baseViewPath . '/' . ltrim($relativePath, '/'));
    }

    /**
     * 뷰를 include하고, e/code/details/메타를 모두 전달
     */
    protected function renderView(string $relativePath, Throwable $e, int $code): void
    {
        $file = $this->baseViewPath . '/' . ltrim($relativePath, '/');
        $details = $this->details($e, $code);
        $meta = static::$metaData;
        $debug = $this->debug;
        extract(['e' => $e, 'code' => $code, 'details' => $details, 'meta' => $meta, 'debug' => $debug]);
        include $file;
    }

    protected function handleCli(Throwable $e): void
    {
        $code = $e instanceof HttpException ? $e->getStatusCode() : 500;
        $this->logger->error($e->getMessage(), $this->details($e, $code));
        echo "[Exception] {$e->getMessage()}" . PHP_EOL;
        if ($this->debug) echo $this->details($e, $code)['trace'] . PHP_EOL;
    }

    protected function handleJson(Throwable $e, int $code): JsonResponse
    {
        $debugData = null;
        if ($this->debug) {
            $details = $this->details($e, $code);
            $debugData = [
                'exception' => $details['exception'],
                'file'      => $details['file'],
                'line'      => $details['line'],
                'trace'     => explode("\n", $details['trace']),
            ];
        }
        $payload = [
            'success' => false,
            'message' => $e->getMessage(),
            'errors'  => [],
        ];
        if (!empty($debugData)) {
            $payload['debug'] = $debugData;
        }
        if (!empty(static::$metaData)) {
            $payload['meta'] = static::$metaData;
        }
        return Response::json($payload, $code);
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

    protected function simpleError(string $title, string $message, Throwable $e = null, bool $debug = false): void
    {
        echo $this->simpleErrorString($title, $message, $e, $debug);
    }

    // 반환용 (Response::html 사용시)
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

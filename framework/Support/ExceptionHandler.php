<?php

namespace Framework\Support;

use Framework\Configurations\ExceptionConfigurator;
use Framework\Http\Response;
use Framework\Support\Exceptions\Http\ForbiddenException;
use Framework\Support\Exceptions\Http\InternalServerErrorException;
use Framework\Support\Exceptions\Http\NotFoundException;
use Framework\Support\Exceptions\Http\UnauthorizedException;
use Framework\Support\Exceptions\HttpException;
use Framework\Support\Exceptions\ValidationException;
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

        if (php_sapi_name() === 'cli') {
            $this->handleCli($e);
            return;
        }

        if (request()->isJsonRequest()) {
            $this->handleJson($e, $code);
            return;
        }

        try {
            $this->handleHtml($e, $code)->send();
        } catch (Throwable $fatal) {
            if (config('app.debug')) {
                http_response_code(500);
                echo '<h1>예외 핸들러 실패</h1>';
                echo '<pre>' . $fatal->getMessage() . '</pre>';
                echo '<pre>' . $fatal->getTraceAsString() . '</pre>';
            } else {
                response()->setStatusCode(500)->view('errors.500')->send();
            }
        }
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

    protected function handleHtml(Throwable $e, int $code): Response
    {
        http_response_code($code);

        if ($e instanceof NotFoundException) {
            return response()
                ->setStatusCode(404)
                ->view('errors.404');
        }

        if ($e instanceof UnauthorizedException) {
            return response()
                ->setStatusCode(401)
                ->view('errors.401');
        }

        if ($e instanceof ForbiddenException) {
            return response()
                ->setStatusCode(403)
                ->view('errors.403');
        }

        if ($e instanceof ValidationException) {
            return back()
                ->withErrors($e->errors())
                ->withInput(request()->all());
        }

        if (!config('app.debug')) {
            return response()
                ->setStatusCode($code)
                ->view('errors.500');
        }

        // 코드 스니펫 추출
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
                    'number' => $i + 1,
                    'code' => rtrim($fileLines[$i]),
                    'highlight' => ($i + 1 === $line),
                ];
            }
        }

        return response()
            ->setStatusCode($code)
            ->view('errors.500-debug', [
                'message' => $e->getMessage(),
                'file' => $file,
                'line' => $line,
                'trace' => $e->getTraceAsString(),
                'exception' => get_class($e),
                'snippet' => $codeLines,
            ]);
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

        response()
            ->json($response, $code)
            ->send();
    }
}

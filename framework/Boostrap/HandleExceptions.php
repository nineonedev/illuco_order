<?php 

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\ExceptionConfigurator;
use Framework\Core\Application;
use Framework\Support\Exceptions\ExceptionHandler;

class HandleExceptions implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var ExceptionConfigurator $configurator */
        $configurator = $app->make(ExceptionConfigurator::class); 
        $handler = new ExceptionHandler(); 
        
        $configurator->use($handler);
        $handler->setConfigurator($configurator);
        $app->instance(ExceptionHandler::class, $handler);

        // 예외 핸들러 등록
        set_exception_handler([$handler, 'handle']);

        // PHP 에러를 예외처럼 던지기
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        register_shutdown_function(function () use ($handler) {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                try {
                    $exception = new \ErrorException(
                        $error['message'], 0, $error['type'], $error['file'], $error['line']
                    );
                    $handler->handle($exception);
                } catch (\Throwable $fatal) {
                    http_response_code(500);
                    echo '서버 내부 오류가 발생했습니다.';
                }
            }
        });
    }
}

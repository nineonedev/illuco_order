<?php 

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Configurations\ExceptionConfigurator;
use Framework\Core\Application;
use Framework\Support\ExceptionHandler;

class SetExceptionHandler implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        /** @var ExceptionConfigurator $config */
        $config = $app->make(ExceptionConfigurator::class); 

        $handler = $config->getHandler() ?? new ExceptionHandler(env('APP_DEBUG', config('app.debug', false))); 

        // 예외 핸들러 등록
        set_exception_handler([$handler, 'handle']);

        // PHP 에러를 예외처럼 던지기
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        // fatal error 캐치
        register_shutdown_function(function () use ($handler) {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $exception = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
                $handler->handle($exception);
            }
        });
    }
}

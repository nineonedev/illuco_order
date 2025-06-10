<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\State\Env;

class RegisterEnvironment implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $file = BASE_PATH . '/.env';
        
        if (!file_exists($file)) {
            output_lines([
                "[Framework Bootstrap Error] .env 파일이 존재하지 않습니다.",
                "",
                "해결 방법:",
                "- .env.example 파일이 있다면 복사해 사용하세요:",
                "    cp .env.example .env",
                "- 또는 환경 설정을 수동으로 작성하세요.",
                "",
                "실패한 경로: {$file}",
            ]);
            die;
        }

        $env = new Env($file); 
        $app->instance(Env::class, $env);
        $app->setEnvironment($env->get('APP_ENV', 'production'));
    }
}
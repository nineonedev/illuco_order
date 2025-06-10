<?php

namespace Framework\Boostrap\Bootstrappers;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Console\Output\Output;
use Framework\Core\Application;
use Framework\State\Config;

class RegisterConfiguration implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $configPath = BASE_PATH . '/config';
        
        if (!is_dir($configPath)) {
            output_lines([
                "[Framework Bootstrap Error] 설정 디렉토리가 존재하지 않습니다.",
                "해결 방법:",
                "- config/ 디렉토리가 누락되었는지 확인하세요."
            ]);
            die; 
        }

        $config = new Config($configPath);
        $app->instance(Config::class, $config);
    }
}

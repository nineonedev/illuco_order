<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;

class SetLocalePrefix implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $server = $_SERVER;
        $path = $server['REQUEST_URI'] ?? '/';

        // 앞에 /ko, /en 등이 붙어 있는지 확인
        $segments = explode('/', trim($path, '/'));
        $locale = $segments[0] ?? null;

        $supported = array_keys(config('translation.languages', []));
        
        if ($locale && in_array($locale, $supported)) {
            // 로케일 설정
            set_locale($locale);

            // REQUEST_URI에서 prefix 제거
            $trimmed = '/' . implode('/', array_slice($segments, 1));
            $_SERVER['REQUEST_URI'] = $trimmed ?: '/';

            // 앱 레벨에서도 기록
            $app->setLocale($locale);
        } else {
            $app->setLocale(config('translation.locale', 'ko'));
        }
    }
}

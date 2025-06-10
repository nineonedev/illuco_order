<?php

namespace Framework\Boostrap;

use Framework\Boostrap\Contracts\BootstrapperInterface;
use Framework\Core\Application;
use Framework\Translation\Contracts\TranslatorInterface;
use Framework\Translation\FileLoader;
use Framework\Translation\Translator;
use Framework\Translation\TranslatorManager; 

class RegisterTranslators implements BootstrapperInterface
{
    public function bootstrap(Application $app): void
    {
        $locale = config('translation.locale'); 
        $fallback = config('translation.fallback'); 
        $default = config('translation.default');
        $translators = config('translation.translators'); 

        $manager = new TranslatorManager($locale, $fallback, $default);

        $app->instance(TranslatorManager::class, $manager); 
        $app->instance(TranslatorInterface::class, $manager); 

        foreach ($translators ?? [] as $name => $dir) {
            $manager->addTranslator($name, new Translator($locale, $fallback, new FileLoader($dir)));
        }
    }
}
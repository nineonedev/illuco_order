<?php 

namespace Framework\Translation;

use Framework\Core\ServiceProvider;
use Framework\Translation\Contracts\TranslatorInterface;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $locale = config('translation.locale'); 
        $fallback = config('translation.fallback'); 
        $default = config('translation.default');
        $translators = config('translation.translators'); 

        $manager = new TranslatorManager($locale, $fallback, $default);

        $this->app->instance(TranslatorManager::class, $manager); 
        $this->app->instance(TranslatorInterface::class, $manager); 

        foreach ($translators ?? [] as $name => $dir) {
            $manager->addTranslator($name, new Translator($locale, $fallback, new FileLoader($dir)));
        }
    }
}
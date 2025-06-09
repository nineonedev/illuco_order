<?php

namespace Framework\Support\Facades;

use Framework\View\ViewRenderer;

/**
 * @method static void setLayout(?string $layout)
 * @method static void share(string $key, $value)
 * @method static void shares(array $data)
 * @method static mixed getShared(?string $key = null, $default = null)
 * @method static string render(string $view, array $data = [])
 * @method static string include(string $view, array $data = [])
 * @method static \Framework\View\SectionManager getSections()
 * @method static \Framework\View\ComponentManager getComponents()
 * @method static void section(string $name)
 * @method static void endSection()
 * @method static bool exists(string $view)
 * @method static string yield(string $name, string $default = '')
 * @method static void component(string $view, array $data = [])
 * @method static string endComponent()
 *
 * @see ViewRenderer
 */
class View extends Facade 
{
    protected static function getFacadeAccessor(): string
    {
        return ViewRenderer::class;
    }
}

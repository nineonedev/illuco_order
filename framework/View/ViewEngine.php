<?php

namespace Framework\View;

use Framework\View\Contracts\ViewFinderInterface;

class ViewEngine
{
    protected ViewFinderInterface $finder;
    protected SectionManager $sections;
    protected ComponentManager $components;

    protected array $shared = [];
    protected ?string $layout = null;

    public function __construct(
        ViewFinderInterface $finder,
        SectionManager $sections,
        ComponentManager $components
    ) {
        $this->finder = $finder;
        $this->sections = $sections;
        $this->components = $components;
    }

    public function exists(string $view): bool
    {
        return $this->finder->exists($view);
    }

    public function setLayout(?string $layout): void
    {
        $this->layout = $layout;
    }

    public function share(string $key, $value): void
    {
        $this->shared[$key] = $value;
    }

    public function shares(array $data): void
    {
        $this->shared = array_merge($this->shared, $data);
    }

    public function getShared(?string $key = null, $default = null)
    {
        return $key === null
            ? $this->shared
            : ($this->shared[$key] ?? $default);
    }

    public function render(string $view, array $data = []): string
    {
        try {
            $content = $this->include($view, $data);

            if ($this->layout !== null) {
                $layout = $this->layout;
                $this->layout = null;

                $content = $this->include($layout, $data);
            }

            return $content;
        } catch (\Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean(); // 버퍼 제거
            }
            
            throw $e; 
            
        } finally {
            $this->sections->flush();
            $this->components->flush();
            $this->layout = null;
        }
    }


    public function include(string $view, array $data = []): string
    {
        $path = $this->finder->find($view);

        extract(array_merge($this->shared, $data), EXTR_SKIP);

        ob_start();
        include $path;
        return ob_get_clean();
    }

    public function getSections(): SectionManager
    {
        return $this->sections;
    }

    public function getComponents(): ComponentManager
    {
        return $this->components;
    }

        public function section(string $name): void
    {
        $this->sections->start($name);
    }

    public function endSection(): void
    {
        $this->sections->stop();
    }

    public function yield(string $name, string $default = ''): string
    {
        return $this->sections->yield($name, $default);
    }

    public function component(string $view, array $data = []): void
    {
        $this->components->start($view, $data);
    }

    public function endComponent(): string
    {
        $info = $this->components->stop();

        return $this->include($info['view'], array_merge(
            $this->shared, $info['data'], ['slot' => $info['slot']]
        ));
    }

}

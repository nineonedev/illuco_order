<?php

namespace Framework\View;

class ViewFactory
{
    protected ViewRenderer $renderer;

    public function __construct(ViewRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(string $view, array $data = []): string
    {
        return $this->renderer->render($view, $data);
    }

    public function layout(string $layout): void
    {
        $this->renderer->setLayout($layout);
    }

    public function share(string $key, $value): void
    {
        $this->renderer->share($key, $value);
    }

    public function shares(array $data): void
    {
        $this->renderer->shares($data);
    }

    public function shared(?string $key = null, $default = null)
    {
        return $this->renderer->getShared($key, $default);
    }

    public function section(string $name): void
    {
        $this->renderer->getSections()->start($name);
    }

    public function endSection(): void
    {
        $this->renderer->getSections()->stop();
    }

    public function yield(string $name, string $default = ''): string
    {
        return $this->renderer->getSections()->yield($name, $default);
    }

    public function component(string $view, array $data = []): void
    {
        $this->renderer->getComponents()->start($view, $data);
    }

    public function endComponent(): string
    {
        $info = $this->renderer->getComponents()->stop();

        return $this->renderer->include($info['view'], array_merge(
            $info['data'], ['slot' => $info['slot']]
        ));
    }
}

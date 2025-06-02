<?php

namespace Framework\View;

class SectionManager
{
    protected array $sections = [];
    protected array $sectionStack = [];

    public function start(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    public function stop(): void
    {
        if (empty($this->sectionStack)) {
            throw new \LogicException("No section has been started.");
        }

        $name = array_pop($this->sectionStack);
        $content = ob_get_clean();

        if (isset($this->sections[$name])) {
            $this->sections[$name] .= $content;
        } else {
            $this->sections[$name] = $content;
        }
    }

    public function yield(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    public function flush(): void
    {
        $this->sections = [];
        $this->sectionStack = [];
    }

    public function getSections(): array
    {
        return $this->sections;
    }
}

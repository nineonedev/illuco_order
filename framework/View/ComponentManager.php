<?php

namespace Framework\View;

class ComponentManager
{
    protected array $componentStack = [];

    public function start(string $view, array $data = []): void
    {
        $this->componentStack[] = [$view, $data];
        ob_start();
    }

    /**
     * @return array{view: string, data: array, slot: string}
     */
    public function stop(): array
    {
        if (empty($this->componentStack)) {
            throw new \LogicException("No component has been started.");
        }

        [$view, $data] = array_pop($this->componentStack);
        $slot = ob_get_clean();

        return [
            'view' => $view,
            'data' => $data,
            'slot' => $slot,
        ];
    }

    public function flush(): void
    {
        $this->componentStack = [];
    }
}

<?php

namespace Framework\Console\UI;

class ProgressBar
{
    protected int $total;
    protected int $current = 0;
    protected int $width = 50;
    protected string $prefix = '';
    protected string $suffix = '';

    public function __construct(int $total, string $prefix = '', string $suffix = '')
    {
        $this->total = max(1, $total); // 최소 1 이상
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    /**
     * 현재 상태를 한 줄로 출력
     */
    protected function display(): void
    {
        $percent = $this->current / $this->total;
        $filled = (int) floor($percent * $this->width);
        $bar = str_repeat('=', $filled) . str_repeat(' ', $this->width - $filled);
        $percentFormatted = str_pad((int) ($percent * 100), 3, ' ', STR_PAD_LEFT);

        printf("\r%s [%s] %s%% %s", $this->prefix, $bar, $percentFormatted, $this->suffix);
        flush();
    }

    public function advance(int $step = 1): void
    {
        $this->current += $step;
        if ($this->current > $this->total) {
            $this->current = $this->total;
        }

        $this->display();
    }

    public function finish(): void
    {
        $this->current = $this->total;
        $this->display();
        echo PHP_EOL;
    }
}

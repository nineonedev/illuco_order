<?php

namespace Framework\Console\Output;

class Output
{
    protected array $colors = [
        'black'   => '30',
        'red'     => '31',
        'green'   => '32',
        'yellow'  => '33',
        'blue'    => '34',
        'magenta' => '35',
        'cyan'    => '36',
        'white'   => '37',
    ];

    /**
     * 일반 메시지 출력 (줄바꿈 없음)
     */
    public function write(string $message, bool $newline = false): void
    {
        echo $message . ($newline ? PHP_EOL : '');
    }

    /**
     * 일반 메시지 출력 (줄바꿈 포함)
     */
    public function writeln(string $message = ''): void
    {
        $this->write($message, true);
    }

    public function info(string $message): void
    {
        $this->writeln($this->color($message, 'cyan'));
    }

    public function success(string $message): void
    {
        $this->writeln($this->color($message, 'green'));
    }

    public function error(string $message): void
    {
        $this->writeln($this->color($message, 'red'));
    }

    public function warn(string $message): void
    {
        $this->writeln($this->color($message, 'yellow'));
    }

    /**
     * ANSI 색상으로 텍스트 감싸기
     */
    protected function color(string $text, string $color): string
    {
        $code = $this->colors[$color] ?? '37'; // default: white
        return "\033[{$code}m{$text}\033[0m";
    }

    /**
     * 줄바꿈 출력
     */
    public function newLine(int $count = 1): void
    {
        echo str_repeat(PHP_EOL, $count);
    }
}

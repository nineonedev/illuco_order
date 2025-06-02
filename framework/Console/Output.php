<?php 

namespace Framework\Console; 

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

    public function write(string $message, bool $newline = false): void
    {
        echo $message . ($newline ? PHP_EOL : ""); 
    }

    public function writeln(string $message): void
    {
        $this->write($message, true); 
    }

    public function info(string $message): void
    {
        $this->write($this->color($message, 'blue'), true);
    }

    public function error(string $message): void
    {
        $this->write($this->color($message, 'red'), true);
    }

    public function success(string $message): void
    {
        $this->write($this->color($message, 'green'), true);
    }

    public function warning(string $message): void
    {
        $this->write($this->color($message, 'yellow'), true);
    }

    public function color(string $text, string $color): string
    {
        $code = $this->colors[$color] ?? '37'; // default to white
        return "\033[{$code}m{$text}\033[0m";
    }

    public function newLine(int $count = 1): void
    {
        echo str_repeat(PHP_EOL, $count);
    }
}
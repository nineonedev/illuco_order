<?php

namespace Framework\Console\Scheduling;

use Closure;
use DateTimeInterface;

class ScheduledCommand
{
    protected string $signature;
    protected Closure $callback;
    protected string $expression = '* * * * *';

    public function __construct(string $signature, Closure $callback)
    {
        $this->signature = $signature;
        $this->callback = $callback;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function cron(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    public function isDue(DateTimeInterface $now): bool
    {
        [$min, $hour, $day, $month, $weekday] = explode(' ', $this->expression);

        return $this->match($min, (int) $now->format('i'))
            && $this->match($hour, (int) $now->format('G'))
            && $this->match($day, (int) $now->format('j'))
            && $this->match($month, (int) $now->format('n'))
            && $this->match($weekday, (int) $now->format('w'));
    }

    protected function match(string $expr, int $value): bool
    {
        if ($expr === '*') return true;

        foreach (explode(',', $expr) as $segment) {
            if (strpos($segment, '/') !== false) {
                [$base, $step] = explode('/', $segment);
                $base = $base === '*' ? 0 : (int)$base;
                if ($value % (int)$step === 0) return true;
            } elseif (strpos($segment, '-') !== false) {
                [$start, $end] = explode('-', $segment);
                if ($value >= (int)$start && $value <= (int)$end) return true;
            } elseif ((int)$segment === $value) {
                return true;
            }
        }

        return false;
    }

    public function run(): void
    {
        call_user_func($this->callback);
    }
}

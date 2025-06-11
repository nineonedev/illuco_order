<?php

namespace Framework\Support;

use DateTime;
use DateInterval;

class DateTimeEx extends DateTime
{
    public function __construct(string $time = 'now')
    {
        parent::__construct($time, Date::timezone());
    }

    public function addSeconds(int $value): self
    {
        return $this->add(new DateInterval("PT{$value}S"));
    }

    public function subSeconds(int $value): self
    {
        return $this->sub(new DateInterval("PT{$value}S"));
    }

    public function addMinutes(int $value): self
    {
        return $this->add(new DateInterval("PT{$value}M"));
    }

    public function subMinutes(int $value): self
    {
        return $this->sub(new DateInterval("PT{$value}M"));
    }

    public function addHours(int $value): self
    {
        return $this->add(new DateInterval("PT{$value}H"));
    }

    public function subHours(int $value): self
    {
        return $this->sub(new DateInterval("PT{$value}H"));
    }

    public function addDays(int $value): self
    {
        return $this->add(new DateInterval("P{$value}D"));
    }

    public function subDays(int $value): self
    {
        return $this->sub(new DateInterval("P{$value}D"));
    }

    public function addWeeks(int $value): self
    {
        return $this->add(new DateInterval("P" . ($value * 7) . "D"));
    }

    public function subWeeks(int $value): self
    {
        return $this->sub(new DateInterval("P" . ($value * 7) . "D"));
    }

    public function copy(): self
    {
        return clone $this;
    }
}
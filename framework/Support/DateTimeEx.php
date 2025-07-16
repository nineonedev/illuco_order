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

    public function startOfDay(): self
    {
        $this->setTime(0, 0, 0);
        return $this;
    }

    public function endOfDay(): self
    {
        $this->setTime(23, 59, 59);
        return $this;
    }

    public function startOfMonth(): self
    {
        $this->setDate((int)$this->format('Y'), (int)$this->format('m'), 1);
        return $this->startOfDay();
    }

    public function endOfMonth(): self
    {
        $this->setDate((int)$this->format('Y'), (int)$this->format('m'), (int)$this->format('t'));
        return $this->endOfDay();
    }

    public function startOfWeek(int $startDay = 1): self
    {
        // ISO 주 시작 요일 기준 (월=1, 일=7)
        $dayOfWeek = (int)$this->format('N'); 
        $diff = $dayOfWeek - $startDay;
        if ($diff < 0) {
            $diff += 7;
        }
        return $this->subDays($diff)->startOfDay();
    }

    public function endOfWeek(int $startDay = 1): self
    {
        return $this->startOfWeek($startDay)->addDays(6)->endOfDay();
    }

    public function setDateComponents(int $year, int $month, int $day): self
    {
        $this->setDate($year, $month, $day);
        return $this;
    }

    public function setTimeComponents(int $hour, int $minute, int $second = 0): self
    {
        $this->setTime($hour, $minute, $second);
        return $this;
    }

    public function isSameDay(self $other): bool
    {
        return $this->format('Y-m-d') === $other->format('Y-m-d');
    }

}
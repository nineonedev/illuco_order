<?php

namespace Framework\Support\ValueObjects;

class Money
{
    protected int $amount;
    protected string $currency;

    public function __construct(int $amount, string $currency = 'KRW')
    {
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function value(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function format(): string
    {
        $symbol = [
            'KRW' => '₩',
            'USD' => '$',
            'EUR' => '€',
            'JPY' => '¥',
        ][$this->currency] ?? '';

        return $symbol . number_format($this->amount / 100, 2);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currencies must match.');
        }
    }
}

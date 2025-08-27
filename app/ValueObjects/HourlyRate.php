<?php

namespace App\ValueObjects;

use InvalidArgumentException;

readonly class HourlyRate
{
    public function __construct(
        public float $amount,
        public string $currency
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Hourly rate cannot be negative.');
        }
        
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a 3-character ISO code.');
        }
    }

    public static function make(float $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function toString(): string
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    public function equals(HourlyRate $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}

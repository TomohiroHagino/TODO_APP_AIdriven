<?php

namespace App\Domain\Shared\ValueObject;

use DateTimeImmutable;

/**
 * 日時値オブジェクト（共通）
 */
class DateTimeValue
{
    private DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    public static function fromString(string $dateTime): self
    {
        return new self(new DateTimeImmutable($dateTime));
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->value->format($format);
    }

    public function equals(DateTimeValue $other): bool
    {
        return $this->value == $other->getValue();
    }

    public function isBefore(DateTimeValue $other): bool
    {
        return $this->value < $other->getValue();
    }

    public function isAfter(DateTimeValue $other): bool
    {
        return $this->value > $other->getValue();
    }

    public function __toString(): string
    {
        return $this->format();
    }
}


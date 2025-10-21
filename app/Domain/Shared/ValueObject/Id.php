<?php

namespace App\Domain\Shared\ValueObject;

use InvalidArgumentException;

/**
 * ID値オブジェクト（共通）
 */
abstract class Id
{
    protected int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('IDは1以上の整数である必要があります');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(Id $other): bool
    {
        return $this->value === $other->getValue() 
            && get_class($this) === get_class($other);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}


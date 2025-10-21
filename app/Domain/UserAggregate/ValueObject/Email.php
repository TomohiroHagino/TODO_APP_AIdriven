<?php

namespace App\Domain\UserAggregate\ValueObject;

use InvalidArgumentException;

/**
 * メールアドレス値オブジェクト
 */
class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('メールアドレスは空にできません');
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('有効なメールアドレスを入力してください');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}


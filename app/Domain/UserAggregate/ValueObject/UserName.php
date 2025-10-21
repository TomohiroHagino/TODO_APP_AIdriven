<?php

namespace App\Domain\UserAggregate\ValueObject;

use InvalidArgumentException;

/**
 * ユーザー名値オブジェクト
 */
class UserName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('ユーザー名は空にできません');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('ユーザー名は255文字以内である必要があります');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(UserName $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}


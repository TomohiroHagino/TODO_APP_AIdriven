<?php

namespace App\Domain\UserAggregate\Event;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;

/**
 * ユーザー作成イベント
 */
class UserCreated
{
    private UserId $userId;
    private UserName $name;
    private Email $email;
    private DateTimeValue $occurredAt;

    public function __construct(
        UserId $userId,
        UserName $name,
        Email $email
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->occurredAt = DateTimeValue::now();
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getOccurredAt(): DateTimeValue
    {
        return $this->occurredAt;
    }
}


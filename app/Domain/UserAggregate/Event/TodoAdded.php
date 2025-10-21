<?php

namespace App\Domain\UserAggregate\Event;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * Todo追加イベント
 */
class TodoAdded
{
    private TodoId $todoId;
    private UserId $userId;
    private TaskTitle $title;
    private DateTimeValue $occurredAt;

    public function __construct(
        TodoId $todoId,
        UserId $userId,
        TaskTitle $title
    ) {
        $this->todoId = $todoId;
        $this->userId = $userId;
        $this->title = $title;
        $this->occurredAt = DateTimeValue::now();
    }

    public function getTodoId(): TodoId
    {
        return $this->todoId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getTitle(): TaskTitle
    {
        return $this->title;
    }

    public function getOccurredAt(): DateTimeValue
    {
        return $this->occurredAt;
    }
}


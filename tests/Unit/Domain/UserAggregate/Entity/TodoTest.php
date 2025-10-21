<?php

namespace Tests\Unit\Domain\UserAggregate\Entity;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\Todo;
use App\Domain\UserAggregate\ValueObject\TaskStatus;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{
    public function test_create_new_todo(): void
    {
        $todoId = new TodoId(1);
        $userId = new UserId(1);
        $title = new TaskTitle('買い物');

        $todo = Todo::create($todoId, $userId, $title);

        $this->assertEquals($todoId, $todo->getId());
        $this->assertEquals($userId, $todo->getUserId());
        $this->assertEquals($title, $todo->getTitle());
        $this->assertTrue($todo->isPending());
        $this->assertFalse($todo->isDone());
        $this->assertInstanceOf(DateTimeValue::class, $todo->getCreatedAt());
    }

    public function test_change_title(): void
    {
        $todo = Todo::create(
            new TodoId(1),
            new UserId(1),
            new TaskTitle('買い物')
        );

        $newTitle = new TaskTitle('掃除');
        $todo->changeTitle($newTitle);

        $this->assertEquals($newTitle, $todo->getTitle());
        $this->assertSame('掃除', $todo->getTitle()->getValue());
    }

    public function test_toggle_status_from_pending_to_done(): void
    {
        $todo = Todo::create(
            new TodoId(1),
            new UserId(1),
            new TaskTitle('買い物')
        );

        $this->assertTrue($todo->isPending());

        $todo->toggleStatus();

        $this->assertTrue($todo->isDone());
        $this->assertFalse($todo->isPending());
    }

    public function test_toggle_status_from_done_to_pending(): void
    {
        $todo = new Todo(
            new TodoId(1),
            new UserId(1),
            new TaskTitle('買い物'),
            TaskStatus::done(),
            DateTimeValue::now()
        );

        $this->assertTrue($todo->isDone());

        $todo->toggleStatus();

        $this->assertTrue($todo->isPending());
        $this->assertFalse($todo->isDone());
    }

    public function test_get_status(): void
    {
        $pendingTodo = Todo::create(
            new TodoId(1),
            new UserId(1),
            new TaskTitle('買い物')
        );

        $doneTodo = new Todo(
            new TodoId(2),
            new UserId(1),
            new TaskTitle('掃除'),
            TaskStatus::done(),
            DateTimeValue::now()
        );

        $this->assertTrue($pendingTodo->getStatus()->isPending());
        $this->assertTrue($doneTodo->getStatus()->isDone());
    }
}


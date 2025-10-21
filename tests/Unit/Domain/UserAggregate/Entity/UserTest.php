<?php

namespace Tests\Unit\Domain\UserAggregate\Entity;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_create_new_user(): void
    {
        $userId = new UserId(1);
        $name = new UserName('山田太郎');
        $email = new Email('test@example.com');
        $password = 'hashed_password';

        $user = UserEntity::create($userId, $name, $email, $password);

        $this->assertEquals($userId, $user->getId());
        $this->assertEquals($name, $user->getName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertSame($password, $user->getPassword());
        $this->assertInstanceOf(DateTimeValue::class, $user->getCreatedAt());
        $this->assertEmpty($user->getTodos());
    }

    public function test_add_todo(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $todoId = new TodoId(1);
        $title = new TaskTitle('買い物');

        $todo = $user->addTodo($todoId, $title);

        $this->assertCount(1, $user->getTodos());
        $this->assertEquals($todoId, $todo->getId());
        $this->assertEquals($title, $todo->getTitle());
        $this->assertTrue($todo->isPending());
    }

    public function test_find_todo(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $todoId1 = new TodoId(1);
        $todoId2 = new TodoId(2);

        $user->addTodo($todoId1, new TaskTitle('買い物'));
        $user->addTodo($todoId2, new TaskTitle('掃除'));

        $foundTodo = $user->findTodo($todoId1);
        $notFoundTodo = $user->findTodo(new TodoId(999));

        $this->assertNotNull($foundTodo);
        $this->assertEquals($todoId1, $foundTodo->getId());
        $this->assertNull($notFoundTodo);
    }

    public function test_remove_todo(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $todoId1 = new TodoId(1);
        $todoId2 = new TodoId(2);

        $user->addTodo($todoId1, new TaskTitle('買い物'));
        $user->addTodo($todoId2, new TaskTitle('掃除'));

        $this->assertCount(2, $user->getTodos());

        $user->removeTodo($todoId1);

        $this->assertCount(1, $user->getTodos());
        $this->assertNull($user->findTodo($todoId1));
        $this->assertNotNull($user->findTodo($todoId2));
    }

    public function test_get_done_todos(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $todo1 = $user->addTodo(new TodoId(1), new TaskTitle('買い物'));
        $todo2 = $user->addTodo(new TodoId(2), new TaskTitle('掃除'));
        $todo3 = $user->addTodo(new TodoId(3), new TaskTitle('洗濯'));

        $todo1->toggleStatus(); // 完了
        $todo3->toggleStatus(); // 完了

        $doneTodos = $user->getDoneTodos();

        $this->assertCount(2, $doneTodos);
    }

    public function test_get_pending_todos(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $todo1 = $user->addTodo(new TodoId(1), new TaskTitle('買い物'));
        $todo2 = $user->addTodo(new TodoId(2), new TaskTitle('掃除'));
        $todo3 = $user->addTodo(new TodoId(3), new TaskTitle('洗濯'));

        $todo2->toggleStatus(); // 完了

        $pendingTodos = $user->getPendingTodos();

        $this->assertCount(2, $pendingTodos);
    }

    public function test_change_name(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $newName = new UserName('佐藤花子');
        $user->changeName($newName);

        $this->assertEquals($newName, $user->getName());
    }

    public function test_change_email(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'password'
        );

        $newEmail = new Email('new@example.com');
        $user->changeEmail($newEmail);

        $this->assertEquals($newEmail, $user->getEmail());
    }

    public function test_change_password(): void
    {
        $user = UserEntity::create(
            new UserId(1),
            new UserName('山田太郎'),
            new Email('test@example.com'),
            'old_password'
        );

        $user->changePassword('new_hashed_password');

        $this->assertSame('new_hashed_password', $user->getPassword());
    }
}


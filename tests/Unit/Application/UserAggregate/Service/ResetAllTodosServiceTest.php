<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\ResetAllTodosService;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

class ResetAllTodosServiceTest extends TestCase
{
    public function test_handle_resets_all_done_todos_to_pending(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        // ユーザー1: 完了Todoが2つ、未完了が1つ
        $user1 = UserEntity::create(
            new UserId(1),
            new UserName('User 1'),
            new Email('user1@example.com'),
            'password'
        );
        $todo1 = $user1->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $todo2 = $user1->addTodo(new TodoId(2), new TaskTitle('Task 2'));
        $todo3 = $user1->addTodo(new TodoId(3), new TaskTitle('Task 3'));
        $todo1->markAsDone();
        $todo2->markAsDone();

        // ユーザー2: 完了Todoが1つ
        $user2 = UserEntity::create(
            new UserId(2),
            new UserName('User 2'),
            new Email('user2@example.com'),
            'password'
        );
        $todo4 = $user2->addTodo(new TodoId(4), new TaskTitle('Task 4'));
        $todo4->markAsDone();

        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$user1, $user2]);

        $repository->expects($this->exactly(2))
            ->method('save');

        $service = new ResetAllTodosService($repository);
        $result = $service->handle();

        // 結果を検証
        $this->assertEquals(2, $result['totalUsers']);
        $this->assertEquals(4, $result['totalTodos']);
        $this->assertEquals(3, $result['resetCount']);

        // Todoのステータスを検証
        $this->assertTrue($todo1->isPending());
        $this->assertTrue($todo2->isPending());
        $this->assertTrue($todo3->isPending());
        $this->assertTrue($todo4->isPending());
    }

    public function test_handle_with_no_done_todos(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('User'),
            new Email('user@example.com'),
            'password'
        );
        $user->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $user->addTodo(new TodoId(2), new TaskTitle('Task 2'));

        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$user]);

        // 完了Todoがないのでsaveは呼ばれない
        $repository->expects($this->never())
            ->method('save');

        $service = new ResetAllTodosService($repository);
        $result = $service->handle();

        $this->assertEquals(1, $result['totalUsers']);
        $this->assertEquals(2, $result['totalTodos']);
        $this->assertEquals(0, $result['resetCount']);
    }

    public function test_handle_with_no_users(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $repository->expects($this->never())
            ->method('save');

        $service = new ResetAllTodosService($repository);
        $result = $service->handle();

        $this->assertEquals(0, $result['totalUsers']);
        $this->assertEquals(0, $result['totalTodos']);
        $this->assertEquals(0, $result['resetCount']);
    }

    public function test_handle_with_user_with_no_todos(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('User'),
            new Email('user@example.com'),
            'password'
        );

        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$user]);

        $repository->expects($this->never())
            ->method('save');

        $service = new ResetAllTodosService($repository);
        $result = $service->handle();

        $this->assertEquals(1, $result['totalUsers']);
        $this->assertEquals(0, $result['totalTodos']);
        $this->assertEquals(0, $result['resetCount']);
    }
}


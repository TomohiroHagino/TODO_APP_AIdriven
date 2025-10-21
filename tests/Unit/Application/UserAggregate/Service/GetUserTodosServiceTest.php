<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\GetUserTodosService;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class GetUserTodosServiceTest extends TestCase
{
    public function test_handle_returns_all_todos(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $user->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $user->addTodo(new TodoId(2), new TaskTitle('Task 2'));

        $repository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new UserId(1)))
            ->willReturn($user);

        $service = new GetUserTodosService($repository);
        $todos = $service->handle(1);

        $this->assertCount(2, $todos);
    }

    public function test_handle_by_status_returns_done_todos(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $todo1 = $user->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $todo2 = $user->addTodo(new TodoId(2), new TaskTitle('Task 2'));
        $todo1->toggleStatus(); // 完了

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $service = new GetUserTodosService($repository);
        $doneTodos = $service->handleByStatus(1, true);

        $this->assertCount(1, $doneTodos);
    }

    public function test_handle_by_status_returns_pending_todos(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $todo1 = $user->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $todo2 = $user->addTodo(new TodoId(2), new TaskTitle('Task 2'));
        $todo1->toggleStatus(); // 完了

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $service = new GetUserTodosService($repository);
        $pendingTodos = $service->handleByStatus(1, false);

        $this->assertCount(1, $pendingTodos);
    }

    public function test_handle_throws_exception_when_user_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $service = new GetUserTodosService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');

        $service->handle(999);
    }
}


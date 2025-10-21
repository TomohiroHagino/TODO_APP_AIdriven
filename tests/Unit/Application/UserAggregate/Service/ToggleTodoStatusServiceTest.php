<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\ToggleTodoStatusService;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ToggleTodoStatusServiceTest extends TestCase
{
    public function test_handle_toggles_todo_status(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $todo = $user->addTodo(new TodoId(1), new TaskTitle('Task'));
        $this->assertTrue($todo->isPending());

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('save')
            ->with($user);

        $service = new ToggleTodoStatusService($repository);
        $service->handle(1, 1);

        $this->assertTrue($todo->isDone());
    }

    public function test_handle_throws_exception_when_user_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $service = new ToggleTodoStatusService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');

        $service->handle(999, 1);
    }

    public function test_handle_throws_exception_when_todo_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $service = new ToggleTodoStatusService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Todoが見つかりません');

        $service->handle(1, 999);
    }
}


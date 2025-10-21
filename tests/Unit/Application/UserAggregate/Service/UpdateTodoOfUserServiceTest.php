<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\UpdateTodoOfUserService;
use App\Domain\UserAggregate\Entity\User;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UpdateTodoOfUserServiceTest extends TestCase
{
    public function test_handle_updates_todo_title(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = User::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $user->addTodo(new TodoId(1), new TaskTitle('Old Title'));

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('save')
            ->with($user);

        $service = new UpdateTodoOfUserService($repository);
        $service->handle(1, 1, 'New Title');

        $todo = $user->findTodo(new TodoId(1));
        $this->assertSame('New Title', $todo->getTitle()->getValue());
    }

    public function test_handle_throws_exception_when_user_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $service = new UpdateTodoOfUserService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');

        $service->handle(999, 1, 'New Title');
    }

    public function test_handle_throws_exception_when_todo_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = User::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $service = new UpdateTodoOfUserService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Todoが見つかりません');

        $service->handle(1, 999, 'New Title');
    }
}


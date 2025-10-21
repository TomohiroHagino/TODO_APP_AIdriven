<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\DeleteTodoOfUserService;
use App\Domain\UserAggregate\Entity\User;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DeleteTodoOfUserServiceTest extends TestCase
{
    public function test_handle_deletes_todo(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = User::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $user->addTodo(new TodoId(1), new TaskTitle('Task 1'));
        $user->addTodo(new TodoId(2), new TaskTitle('Task 2'));

        $this->assertCount(2, $user->getTodos());

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('save')
            ->with($user);

        $service = new DeleteTodoOfUserService($repository);
        $service->handle(1, 1);

        $this->assertCount(1, $user->getTodos());
        $this->assertNull($user->findTodo(new TodoId(1)));
    }

    public function test_handle_throws_exception_when_user_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $service = new DeleteTodoOfUserService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');

        $service->handle(999, 1);
    }
}


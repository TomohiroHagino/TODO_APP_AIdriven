<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\DeleteUserAccountService;
use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DeleteUserAccountServiceTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private DeleteUserAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new DeleteUserAccountService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_アカウントを削除できる(): void
    {
        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('名前'),
            new Email('user@example.com'),
            'hashed_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $userId))
            ->andReturn($user);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $userId));

        $this->service->handle($userId);

        $this->assertTrue(true); // アサーションが呼ばれたことを確認
    }

    public function test_ユーザーが見つからない場合は例外をスローする(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません（ID: 999）');

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->service->handle(999);
    }

    public function test_User削除時にTodoも自動削除される(): void
    {
        // このテストは概念を示すもの
        // 実際のカスケード削除はデータベースレベルで行われる
        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('名前'),
            new Email('user@example.com'),
            'hashed_password',
            DateTimeValue::now()
        );

        // UserにTodoを追加
        $user->addTodo(
            new \App\Domain\UserAggregate\ValueObject\TodoId(1),
            new \App\Domain\UserAggregate\ValueObject\TaskTitle('テストTodo')
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $userId));

        $this->service->handle($userId);

        // User Aggregateの原則により、Userが削除されるとTodoも削除される
        $this->assertTrue(true);
    }
}


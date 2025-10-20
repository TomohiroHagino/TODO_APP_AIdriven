<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\ListTodosService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\ValueObject\TaskTitle;

// ListTodosServiceのユニットテスト
class ListTodosServiceTest extends TestCase
{
    // findAllで全件取得できることをテスト
    public function test_handle_returns_all_todos()
    {
        // テスト用のTodo配列を用意
        $todos = [
            new Todo(1, new TaskTitle('A'), false, new \DateTimeImmutable()),
            new Todo(2, new TaskTitle('B'), true, new \DateTimeImmutable())
        ];

        // TodoRepositoryのモック
        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('findAll')->willReturn($todos);

        $service = new ListTodosService($repository);
        $result = $service->handle();

        $this->assertCount(2, $result);
        $this->assertSame($todos, $result);
    }

    // findByStatusで条件付き取得ができることをテスト
    public function test_handleByStatus_returns_filtered_todos()
    {
        // 完了済みだけリストに
        $todos = [
            new Todo(2, new TaskTitle('B'), true, new \DateTimeImmutable())
        ];

        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('findByStatus')->with(true)->willReturn($todos);

        $service = new ListTodosService($repository);
        $result = $service->handleByStatus(true);

        $this->assertCount(1, $result);
        $this->assertSame($todos, $result);
    }
}

<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\GetTodoDetailService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\ValueObject\TaskTitle;

// GetTodoDetailServiceのユニットテスト
class GetTodoDetailServiceTest extends TestCase
{
    // 存在するIDでTodoが取得できることをテスト
    public function test_handle_returns_todo_when_found()
    {
        $id = 1;
        $todo = new Todo($id, new TaskTitle('読書'), false, new \DateTimeImmutable());

        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->with($id)->willReturn($todo);

        $service = new GetTodoDetailService($repository);
        $result = $service->handle($id);

        $this->assertSame($todo, $result);
    }

    // 存在しないIDの場合はnullが返されることをテスト
    public function test_handle_returns_null_when_not_found()
    {
        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->willReturn(null);

        $service = new GetTodoDetailService($repository);
        $result = $service->handle(999);

        $this->assertNull($result);
    }
}

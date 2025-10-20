<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\ToggleTodoStatusService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\ValueObject\TaskTitle;

// ToggleTodoStatusServiceのユニットテスト
class ToggleTodoStatusServiceTest extends TestCase
{
    // 正常に状態が切り替わり、saveが呼ばれることをテスト
    public function test_handle_toggles_status_and_saves()
    {
        $id = 1;
        $todo = new Todo($id, new TaskTitle('読書'), false, new \DateTimeImmutable());

        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->with($id)->willReturn($todo);
        $repository->expects($this->once())->method('save')->with($todo);

        $service = new ToggleTodoStatusService($repository);
        $service->handle($id);

        // 状態が切り替わったことを確認
        $this->assertTrue($todo->isDone());
    }

    // 存在しないIDの場合は例外が発生することをテスト
    public function test_handle_throws_when_todo_not_found()
    {
        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->willReturn(null);

        $service = new ToggleTodoStatusService($repository);
        $this->expectException(\RuntimeException::class);
        $service->handle(999);
    }
}

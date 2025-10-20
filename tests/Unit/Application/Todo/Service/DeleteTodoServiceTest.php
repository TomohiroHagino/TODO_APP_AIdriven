<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\DeleteTodoService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;

// DeleteTodoServiceのユニットテスト
class DeleteTodoServiceTest extends TestCase
{
    // deleteが指定IDで呼ばれることをテスト
    public function test_handle_calls_delete_with_correct_id()
    {
        $id = 1;
        $repository = $this->createMock(TodoRepositoryInterface::class);

        // delete()が1回、引数idで呼ばれることを検証
        $repository->expects($this->once())->method('delete')->with($id);

        $service = new DeleteTodoService($repository);
        $service->handle($id);
    }
}

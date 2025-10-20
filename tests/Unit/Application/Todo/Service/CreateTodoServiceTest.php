<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\CreateTodoService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;

// CreateTodoServiceのユニットテスト
class CreateTodoServiceTest extends TestCase
{
    // 正常にTodoがsaveされることを確認
    public function test_handle_creates_and_saves_todo()
    {
        // TodoRepositoryのモック作成
        $repository = $this->createMock(TodoRepositoryInterface::class);

        // nextId()の返り値を決めておく
        $repository->method('nextId')->willReturn(1);

        // save()が1回呼ばれることを期待
        $repository->expects($this->once())->method('save')->with($this->callback(function ($todo) {
            // Todoエンティティであることとタイトル等の検証
            /** @var Todo $todo */
            return $todo instanceof Todo
                && (string)$todo->getTitle() === '買い物'
                && $todo->getId() === 1
                && $todo->isDone() === false;
        }));

        // Serviceインスタンス生成＆実行
        $service = new CreateTodoService($repository);
        $service->handle('買い物');
    }
}

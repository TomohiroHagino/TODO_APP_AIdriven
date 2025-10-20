<?php

namespace Tests\Unit\Application\Todo\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Todo\Service\UpdateTodoService;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\ValueObject\TaskTitle;

// UpdateTodoServiceのユニットテスト
class UpdateTodoServiceTest extends TestCase
{
    // 指定IDのTodoタイトルが正常に更新・保存されることをテスト
    public function test_handle_updates_title_and_saves_todo()
    {
        $id = 1;
        $beforeTitle = new TaskTitle('読書');
        $afterTitle = '運動';
        $todo = new Todo($id, $beforeTitle, false, new \DateTimeImmutable());

        // モック
        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->with($id)->willReturn($todo);
        $repository->expects($this->once())->method('save')->with($this->callback(function ($savedTodo) use ($id, $afterTitle) {
            // ID・タイトルが正しいかチェック
            return $savedTodo instanceof Todo && $savedTodo->getId() === $id && (string)$savedTodo->getTitle() === $afterTitle;
        }));

        // Service実行
        $service = new UpdateTodoService($repository);
        $service->handle($id, $afterTitle);
    }

    // 存在しないIDが指定された場合は例外が発生
    public function test_handle_throws_when_todo_not_found()
    {
        $repository = $this->createMock(TodoRepositoryInterface::class);
        $repository->method('find')->willReturn(null);
        $service = new UpdateTodoService($repository);
        $this->expectException(\RuntimeException::class);
        $service->handle(99, '新しいタイトル');
    }
}

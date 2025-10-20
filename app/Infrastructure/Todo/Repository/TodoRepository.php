<?php

namespace App\Infrastructure\Todo\Repository;

use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\ValueObject\TaskTitle;
use App\Models\TodoModel;

/**
 * TodoリポジトリのEloquent実装
 */
class TodoRepository implements TodoRepositoryInterface
{
    /**
     * 次に採番するIDを発行する
     */
    public function nextId(): int
    {
        $maxId = TodoModel::max('id');
        return $maxId ? $maxId + 1 : 1;
    }

    /**
     * ToDoを保存する
     */
    public function save(Todo $todo): void
    {
        TodoModel::updateOrCreate(
            ['id' => $todo->getId()],
            [
                'title' => $todo->getTitle()->getValue(),
                'is_done' => $todo->isDone(),
                'created_at' => $todo->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * 全件取得
     * @return Todo[]
     */
    public function findAll(): array
    {
        return TodoModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->all();
    }

    /**
     * 完了/未完了で絞込取得
     * @param bool $isDone
     * @return Todo[]
     */
    public function findByStatus(bool $isDone): array
    {
        return TodoModel::where('is_done', $isDone)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->all();
    }

    /**
     * ID指定でTodoを取得
     * @param int $id
     * @return Todo|null
     */
    public function find(int $id): ?Todo
    {
        $model = TodoModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    /**
     * タスクを削除する
     * @param int $id
     */
    public function delete(int $id): void
    {
        TodoModel::destroy($id);
    }

    /**
     * EloquentモデルをドメインエンティティTodoに変換
     */
    private function toDomain(TodoModel $model): Todo
    {
        return new Todo(
            $model->id,
            new TaskTitle($model->title),
            $model->is_done,
            new \DateTimeImmutable($model->created_at)
        );
    }
}


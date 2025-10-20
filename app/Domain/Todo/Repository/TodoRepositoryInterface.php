<?php

namespace App\Domain\Todo\Repository;

use App\Domain\Todo\Entity\Todo;

interface TodoRepositoryInterface
{
    /**
     * 次に採番するIDを発行する
     */
    public function nextId(): int;

    /**
     * ToDoを保存する
     */
    public function save(Todo $todo): void;

    /**
     * 全件取得
     * @return Todo[]
     */
    public function findAll(): array;

    /**
     * 完了/未完了で絞込取得
     * @param bool $isDone
     * @return Todo[]
     */
    public function findByStatus(bool $isDone): array;

    /**
     * ID指定でTodoを取得
     * @param int $id
     * @return Todo|null
     */
    public function find(int $id): ?Todo;

    /**
     * タスクを削除する
     * @param int $id
     */
    public function delete(int $id): void;
}

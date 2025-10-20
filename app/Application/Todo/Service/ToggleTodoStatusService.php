<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;

class ToggleTodoStatusService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 完了/未完了状態を切り替え
     * @param int $id
     * @throws \RuntimeException
     */
    public function handle(int $id): void
    {
        $todo = $this->repository->find($id);
        if (!$todo) {
            throw new \RuntimeException('タスクが見つかりませんでした');
        }
        $todo->toggleStatus();
        $this->repository->save($todo);
    }
}

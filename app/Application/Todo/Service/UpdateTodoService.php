<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\ValueObject\TaskTitle;

class UpdateTodoService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * タイトル変更（ID指定＆新タイトル）
     * @param int $id
     * @param string $newTitle
     * @throws \RuntimeException
     */
    public function handle(int $id, string $newTitle): void
    {
        $todo = $this->repository->find($id);
        if (!$todo) {
            throw new \RuntimeException('タスクが見つかりませんでした');
        }
        $todo->changeTitle(new TaskTitle($newTitle));
        $this->repository->save($todo);
    }
}

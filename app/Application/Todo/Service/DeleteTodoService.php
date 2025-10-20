<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;

class DeleteTodoService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID指定でタスク(Todo)を削除
     * @param int $id
     */
    public function handle(int $id): void
    {
        $this->repository->delete($id);
    }
}

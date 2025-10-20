<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;

class GetTodoDetailService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ID指定でTodo詳細を取得
     * @param int $id
     * @return Todo|null
     */
    public function handle(int $id): ?Todo
    {
        return $this->repository->find($id);
    }
}

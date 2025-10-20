<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;

class ListTodosService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 全件取得
     * @return array
     */
    public function handle(): array
    {
        return $this->repository->findAll();
    }

    /**
     * 完了/未完了で絞り込んで取得
     * @param bool $isDone
     * @return array
     */
    public function handleByStatus(bool $isDone): array
    {
        return $this->repository->findByStatus($isDone);
    }
}

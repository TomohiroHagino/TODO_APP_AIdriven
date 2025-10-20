<?php

namespace App\Application\Todo\Service;

use App\Domain\Todo\Repository\TodoRepositoryInterface;
use App\Domain\Todo\Entity\Todo;
use App\Domain\Todo\ValueObject\TaskTitle;

class CreateTodoService
{
    private TodoRepositoryInterface $repository;

    public function __construct(TodoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 新しいタスクを生成してリポジトリに保存する
     */
    public function handle(string $title): void
    {
        $id = $this->repository->nextId();
        $todo = new Todo($id, new TaskTitle($title), false, new \DateTimeImmutable());
        $this->repository->save($todo);
    }
}

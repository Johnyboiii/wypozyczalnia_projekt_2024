<?php

namespace App\Service;

use App\Dto\TaskListFiltersDto;
use App\Dto\TaskListInputFiltersDto;
use App\Entity\Category;
use App\Entity\Enum\TaskStatus;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Task service.
 */
class TaskService implements TaskServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * TaskService constructor.
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param PaginatorInterface $paginator Paginator
     * @param TagServiceInterface $tagService Tag service
     * @param TaskRepository $taskRepository Task repository
     */
    public function __construct(
        private readonly CategoryServiceInterface $categoryService,
        private readonly PaginatorInterface $paginator,
        private readonly TagServiceInterface $tagService,
        private readonly TaskRepository $taskRepository
    ) {
    }

    /**
     * Find all tasks.
     *
     * @param int $page
     * @param User|null $author
     * @param TaskListInputFiltersDto $filtersDto
     * @return PaginationInterface Task entities
     */
    public function getPaginatedList(int $page, User $author = null, TaskListInputFiltersDto $filtersDto): PaginationInterface
    {
        $filters = $this->prepareFilters($filtersDto);

        return $this->paginator->paginate(
            $this->taskRepository->queryByAuthor($author, $filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Task $task): void
    {
        $this->taskRepository->delete($task);
    }

    public function getTasksByCategory(Category $category): array
    {
        return $this->taskRepository->findByCategory($category);
    }

    public function getTasksByTag(Tag $tag): array
    {
        return $this->taskRepository->findByTag($tag);
    }

    private function prepareFilters(TaskListInputFiltersDto $filters): TaskListFiltersDto
    {
        return new TaskListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            new TaskStatus($filters->statusId)
        );
    }
}
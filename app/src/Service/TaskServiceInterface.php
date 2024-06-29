<?php

/**
 * Task service interface.
 */

namespace App\Service;

use App\Dto\TaskListInputFiltersDto;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TaskServiceInterface.
 */
interface TaskServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                     $page       Page number
     * @param User|null               $author     Author
     * @param TaskListInputFiltersDto $filtersDto
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author = null, TaskListInputFiltersDto $filtersDto): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void;

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void;

    /**
     * Get tasks by category.
     *
     * @param int $categoryId Category ID
     *
     * @return array Tasks in the specified category
     */
    public function getTasksByCategory(int $categoryId): array;

    /**
     * Get tasks by tag.
     *
     * @param Tag $tag Tag entity
     *
     * @return array Tasks associated with the specified tag
     */
    public function getTasksByTag(Tag $tag): array;
}

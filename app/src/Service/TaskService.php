<?php

/**
 * TaskService.
 */

namespace App\Service;

use App\Dto\TaskListFiltersDto;
use App\Dto\TaskListInputFiltersDto;
use App\Entity\Category;
use App\Entity\Enum\TaskStatus;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param PaginatorInterface       $paginator       Paginator
     * @param TagServiceInterface      $tagService      Tag service
     * @param TaskRepository           $taskRepository  Task repository
     * @param EntityManagerInterface   $entityManager   Entity manager
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly PaginatorInterface $paginator, private readonly TagServiceInterface $tagService, private readonly TaskRepository $taskRepository, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Find all tasks.
     *
     * @param  int                     $page       The page number
     * @param  User|null               $author     The author of the tasks
     * @param  TaskListInputFiltersDto $filtersDto Filters for the task list
     *
     * @return PaginationInterface                 Task entities
     */
    public function getPaginatedList(int $page, ?User $author = null, TaskListInputFiltersDto $filtersDto): PaginationInterface
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

    /**
     * Get tasks by category.
     *
     * @param Category|int $categoryId The category to filter tasks by
     *
     * @return array An array of Task objects belonging to the specified category
     */
    public function getTasksByCategory(Category|int $categoryId): array
    {
        return $this->taskRepository->findByCategory($categoryId);
    }

    /**
     * Get tasks by tag.
     *
     * @param  Tag $tag The tag to filter tasks by
     *
     * @return array    An array of Task objects with the specified tag
     */
    public function getTasksByTag(Tag $tag): array
    {
        return $this->taskRepository->findByTag($tag);
    }

    /**
     * Get tasks by status.
     *
     * @param array $statuses Array of statuses
     *
     * @return array Tasks with the given statuses
     */
    public function getTasksByStatus(array $statuses): array
    {
        return $this->taskRepository->findBy(['reservationStatus' => $statuses]);
    }

    /**
     * Approve a task.
     *
     * @param Task $task Task entity
     */
    public function approveTask(Task $task): void
    {
        if ($task->getReservationStatus() === 'Oczekujące' || $task->getReservationStatus() === 'Zarezerwowane') {
            $task->setReservationStatus('Zatwierdzone');
            $this->entityManager->flush();
        }
    }

    /**
     * Reject a task.
     *
     * @param Task $task Task entity
     */
    public function rejectTask(Task $task): void
    {
        $task->setReservationStatus('Odrzucone');
        $this->entityManager->flush();
    }

    /**
     * Lend a task.
     *
     * @param Task $task Task entity
     */
    public function lendTask(Task $task): void
    {
        if (in_array($task->getReservationStatus(), ['Zatwierdzone', 'Zarezerwowane', 'Zwrócone'])) {
            $task->setReservationStatus('Wypożyczone');
            $task->setStatus(TaskStatus::STATUS_2);
            $this->entityManager->flush();
        }
    }

    /**
     * Return a task.
     *
     * @param Task $task Task entity
     */
    public function returnTask(Task $task): void
    {
        if ($task->getReservationStatus() === 'Wypożyczone') {
            $task->setReservationStatus('Zwrócone');
            $task->setStatus(TaskStatus::STATUS_1);
            $this->entityManager->flush();
        }
    }

    /**
     * Prepare filters for task list.
     *
     * @param  TaskListInputFiltersDto $filters The filters to prepare
     *
     * @return TaskListFiltersDto               The prepared filters
     */
    private function prepareFilters(TaskListInputFiltersDto $filters): TaskListFiltersDto
    {
        return new TaskListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            new TaskStatus($filters->statusId)
        );
    }
}

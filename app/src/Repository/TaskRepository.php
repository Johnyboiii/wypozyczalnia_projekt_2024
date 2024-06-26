<?php

/**
 * Task repository.
 */

namespace App\Repository;

use App\Dto\TaskListFiltersDto;
use App\Entity\Category;
use App\Entity\Enum\TaskStatus;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskRepository.
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Query all records.
     *
     * @param TaskListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(TaskListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('task')
            ->select(
                'partial task.{id, createdAt, updatedAt, title}',
                'partial category.{id, title}'
            )
            ->join('task.category', 'category')
            ->orderBy('task.updatedAt', 'DESC');

        if ($filters->category) {
            $queryBuilder->andWhere('task.category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag) {
            $queryBuilder->andWhere(':tag MEMBER OF task.tags')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->taskStatus) {
            $queryBuilder->andWhere('task.status = :status')
                ->setParameter('status', $filters->taskStatus->getStatus());
        }

        return $queryBuilder;
    }

    /**
     * Count tasks by category.
     *
     * @param Category $category Category
     *
     * @return int Number of tasks in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('task.id'))
            ->where('task.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
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
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($task);
        $this->_em->flush();
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
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($task);
        $this->_em->flush();
    }

    /**
     * Query tasks by author.
     *
     * @param UserInterface|null $user    User entity
     * @param TaskListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(?UserInterface $user, TaskListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        // Jeśli użytkownik nie jest null i nie jest administratorem, dodaj warunek na autora
        if (null !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder->andWhere('task.author = :author')
                ->setParameter('author', $user);
        }

        return $queryBuilder;
    }

    /**
     * Query tasks by author and status.
     *
     * @param User|null $user   User entity
     * @param int|null  $status Task status
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthorAndStatus(?User $user, ?int $status): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (null !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder->andWhere('task.author = :author')
                ->setParameter('author', $user);
        }

        if (null !== $status) {
            $queryBuilder->andWhere('task.status = :status')
                ->setParameter('status', $status);
        }

        return $queryBuilder;
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder       $queryBuilder Query builder
     * @param TaskListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, TaskListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->taskStatus instanceof TaskStatus) {
            $queryBuilder->andWhere('task.status = :status')
                ->setParameter('status', $filters->taskStatus->getStatus(), Types::INTEGER);
        }

        return $queryBuilder;
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('task');
    }
}

<?php

/**
 * TagRepository.
 */

namespace App\Repository;

use App\Entity\Tag;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * Class TagRepository.
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * TagRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Find tags by title.
     *
     * @param  string $title The title to search for
     *
     * @return Tag[]         An array of Tag objects
     */
    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tags created after a specific date.
     *
     * @param  DateTime $date The date to search from
     *
     * @return Tag[]          An array of Tag objects
     */
    public function findByCreatedAfter(DateTime $date): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.createdAt > :date')
            ->setParameter('date', $date)
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the number of tags.
     *
     * @return int The number of tags
     */
    public function countTags(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find one by id.
     *
     * @param  int $id The ID of the tag
     *
     * @return Tag|null The found Tag or null
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Base query builder for reusable queries.
     *
     * @return QueryBuilder The base query builder
     */
    private function baseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.title', 'ASC');
    }
}

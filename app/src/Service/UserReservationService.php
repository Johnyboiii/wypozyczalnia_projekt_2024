<?php

/**
 * UserReservationService.
 */

namespace App\Service;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserReservationService.
 *
 * This service provides methods to find tasks reserved by a user.
 */
class UserReservationService implements UserReservationServiceInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Find all tasks reserved by a user.
     *
     * @param User $user The user entity
     *
     * @return array<Task>
     */
    public function findTasksReservedByUser(User $user): array
    {
        return $this->entityManager
            ->getRepository(Task::class)
            ->findBy(['reservedBy' => $user]);
    }
}

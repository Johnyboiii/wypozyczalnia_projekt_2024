<?php

/**
 * UserReservation interface.
 */

namespace App\Service;

use App\Entity\User;
use App\Entity\Task;

/**
 * Interface UserReservationServiceInterface.
 *
 * This interface defines methods for handling user reservation-related operations.
 */
interface UserReservationServiceInterface
{
    /**
     * Find all tasks reserved by a user.
     *
     * @param User $user The user entity
     *
     * @return array<Task>
     */
    public function findTasksReservedByUser(User $user): array;
}

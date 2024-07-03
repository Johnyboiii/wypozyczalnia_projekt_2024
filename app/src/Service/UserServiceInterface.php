<?php

/**
 * User Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Save a user entity.
     *
     * @param User $user User Param
     */
    public function save(User $user): void;

    /**
     * Register a new user.
     *
     * @param User   $user          The user entity to be registered
     * @param string $plainPassword The plain password for the user
     *
     * @return User The registered user entity
     */
    public function register(User $user, string $plainPassword): User;

    /**
     * Find all users.
     *
     * @return array<User>
     */
    public function findAllUsers(): array;

    /**
     * Save a user.
     *
     * @param User $user The user to save
     */
    public function saveUser(User $user): void;
}

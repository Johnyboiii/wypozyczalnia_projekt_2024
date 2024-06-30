<?php

/**
 * User Service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Service for handling user-related operations.
 */
class UserService implements UserServiceInterface
{
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    /**
     * UserService constructor.
     *
     * @param UserRepository              $userRepository The user repository
     * @param UserPasswordHasherInterface $passwordHasher The password hasher
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Save a user entity.
     *
     * @param User $user The user entity to save
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Register a new user.
     *
     * @param User   $user          The user entity to be registered
     * @param string $plainPassword The plain password to be hashed and set for the user
     *
     * @return User The registered user entity
     */
    public function register(User $user, string $plainPassword): User
    {
        // Encode the plain password
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        // Save the user using UserRepository
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Find all users.
     *
     * @return array<User>
     */
    public function findAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Save a user.
     *
     * @param User $user The user to save
     */
    public function saveUser(User $user): void
    {
        // You can implement additional logic here if needed
        $this->userRepository->save($user);
    }
}

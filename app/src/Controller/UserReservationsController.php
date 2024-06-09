<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/reservations')]
class UserReservationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'user_reservations_index', methods: ['GET'])]
    public function index(): Response
    {
        // Pobierz aktualnie zalogowanego użytkownika
        $user = $this->getUser();

        // Pobierz wszystkie zadania zarezerwowane przez tego użytkownika
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy(['reservedBy' => $user]);

        // Wyrenderuj widok z listą zadań
        return $this->render('user_reservations/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
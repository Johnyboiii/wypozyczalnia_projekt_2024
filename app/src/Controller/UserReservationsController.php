<?php

/**
 * UserReservations Controller.
 */

namespace App\Controller;

use App\Service\UserReservationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing user reservations.
 *
 * This controller handles operations related to user reservations,
 * such as retrieving and displaying tasks reserved by the current user.
 *
 * @Route("/user/reservations")
 */
#[Route('/user/reservations')]
class UserReservationsController extends AbstractController
{
    private UserReservationServiceInterface $reservationService;

    /**
     * UserReservationsController constructor.
     *
     * @param UserReservationServiceInterface $reservationService The user reservation service
     */
    public function __construct(UserReservationServiceInterface $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Index action to display user's reservations.
     *
     * @Route('/', name: 'user_reservations_index', methods: ['GET'])
     *
     * @return Response The Response Object
     */
    #[Route('/', name: 'user_reservations_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $tasks = $this->reservationService->findTasksReservedByUser($user);

        return $this->render('user_reservations/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}

<?php
/**
 * Public task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\ReservationType;
use App\Service\TaskServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PublicTaskController.
 */
#[Route('/public/task')]
class PublicTaskController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService Task service
     */
    private EntityManagerInterface $entityManager;

    public function __construct(TaskServiceInterface $taskService, EntityManagerInterface $entityManager)
    {
        $this->taskService = $taskService;
        $this->entityManager = $entityManager;
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'public_task_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedList($page, null);

        return $this->render('public_task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'public_task_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET', )]
    public function show(Task $task): Response
    {
        return $this->render('public_task/show.html.twig', ['task' => $task]);
    }

    #[Route('/{id}/reserve', name: 'public_task_reserve', methods: ['GET', 'POST'])]
    public function reserve(Request $request, Task $task): Response
    {
        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sprawdź, czy użytkownik jest zalogowany
            if ($this->getUser()) {
                $task->setReservationStatus('Zarezerwowane');
                $task->setReservedBy($this->getUser());
            } else {
                $task->setReservationStatus('Oczekujące');
                $task->setReservedByEmail($form->get('email')->getData());
            }
            $task->setNickname($form->get('nickname')->getData());//DODANA LINIA
            $task->setReservationComment($form->get('reservationComment')->getData());
            $this->entityManager->flush();

            $this->addFlash('success', 'Reservation has been made successfully.');

            return $this->redirectToRoute('public_task_show', ['id' => $task->getId()]);
        }

        return $this->render('public_task/reserve.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{id}', name: 'public_task_category', methods: ['GET'])]
    public function category(int $id): Response
    {
        // Pobierz wszystkie zadania dla danej kategorii
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy(['category' => $id]);

        // Wyrenderuj widok z listą zadań
        return $this->render('public_task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/tag/{name}', name: 'public_task_tag', methods: ['GET'])]
    public function tag(string $name): Response
    {
        // Pobierz tag o danej nazwie
        $tag = $this->entityManager
            ->getRepository(Tag::class)
            ->findOneBy(['name' => $name]);

        // Pobierz wszystkie zadania dla danego tagu
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy(['tags' => $tag]);

        // Wyrenderuj widok z listą zadań
        return $this->render('public_task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
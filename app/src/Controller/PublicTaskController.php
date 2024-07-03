<?php

/**
 * Public task controller.
 */

namespace App\Controller;

use App\Dto\TaskListInputFiltersDto;
use App\Entity\Enum\TaskStatus;
use App\Entity\Tag;
use App\Entity\Task;
use App\Form\Type\ReservationType;
use App\Service\TaskServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class PublicTaskController.
 */
#[Route('/public/task')]
class PublicTaskController extends AbstractController
{
    private TaskServiceInterface $taskService;

    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService Task service
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Index action.
     *
     * @param Request $request The request object
     * @param int     $page    Page number (optional)
     *
     * @return Response HTTP response
     */
    #[Route(name: 'public_task_index', methods: 'GET')]
    public function index(Request $request, #[MapQueryParameter] int $page = 1): Response
    {
        // Create a new TaskListInputFiltersDto instance and set statusId to TaskStatus::STATUS_1
        $filtersDto = new TaskListInputFiltersDto(categoryId: $request->query->getInt('categoryId'), tagId: $request->query->getInt('tagId'), statusId: TaskStatus::STATUS_1);
        // Pass the TaskListInputFiltersDto instance as the third argument
        $pagination = $this->taskService->getPaginatedList($page, null, $filtersDto);

        return $this->render('public_task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'public_task_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Task $task): Response
    {
        return $this->render('public_task/show.html.twig', ['task' => $task]);
    }

    /**
     * Reserve action.
     *
     * @param Request $request The request object
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
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
            $task->setNickname($form->get('nickname')->getData());

            $task->setReservationComment($form->get('reservationComment')->getData());
            $this->taskService->save($task);
            $this->addFlash('success', 'Reservation has been made successfully.');

            return $this->redirectToRoute('public_task_show', ['id' => $task->getId()]);
        }

        return $this->render('public_task/reserve.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Category action.
     *
     * @param int $id Category ID
     *
     * @return Response HTTP response
     */
    #[Route('/category/{id}', name: 'public_task_category', methods: ['GET'])]
    public function category(int $id): Response
    {
        $tasks = $this->taskService->getTasksByCategory($id);

        return $this->render('public_task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Tag action.
     *
     * @param string $name Tag name
     *
     * @return Response HTTP response
     */
    #[Route('/tag/{name}', name: 'public_task_tag', methods: ['GET'])]
    public function tag(string $name): Response
    {
        $tasks = $this->taskService->getTasksByTag($name);

        return $this->render('public_task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}

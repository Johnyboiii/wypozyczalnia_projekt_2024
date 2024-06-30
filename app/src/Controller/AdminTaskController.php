<?php

/**
 * AdminTaskController.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for admin tasks.
 *
 * @Route("/admin/task")
 *
 * @IsGranted('ROLE_ADMIN')
 */
#[Route('/admin/task')]
#[IsGranted('ROLE_ADMIN')]
class AdminTaskController extends AbstractController
{
    private TaskService $taskService;

    /**
     * AdminTaskController constructor.
     *
     * @param TaskService $taskService The task service instance
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display the index page.
     *
     * @Route("/", name: "admin_task_index", methods: ["GET"])
     */
    #[Route('/', name: 'admin_task_index', methods: ['GET'])]
    public function index(): Response
    {
        // Pobierz wszystkie zadania z bazy danych
        $tasks = $this->taskService->getTasksByStatus(['Zarezerwowane', 'Oczekujące', 'Zatwierdzone', 'Wypożyczone', 'Zwrócone']);

        // Wyrenderuj widok z listą zadań
        return $this->render('admin_task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Approve a task.
     *
     * @Route("/{id}/approve", name="admin_task_approve", methods={"POST"})
     *
     * @param  Task $task The task entity to approve
     *
     * @return Response
     */
    #[Route('/{id}/approve', name: 'admin_task_approve', methods: ['POST'])]
    public function approve(Task $task): Response
    {
        $this->taskService->approveTask($task);

        return $this->redirectToRoute('admin_task_index');
    }

    /**
     * Reject a task.
     *
     * @Route("/{id}/reject", name: "admin_task_reject", methods: ["POST"])
     *
     * @param  Task $task The task entity to approve
     *
     * @return Response
     */
    #[Route('/{id}/reject', name: 'admin_task_reject', methods: ['POST'])]
    public function reject(Task $task): Response
    {
        $this->taskService->rejectTask($task);

        return $this->redirectToRoute('admin_task_index');
    }

    /**
     * Lend a task.
     *
     * @Route("/{id}/lend", name: "admin_task_lend", methods: ["POST"])
     *
     * @param  Task $task The task entity to approve
     *
     * @return Response
     */
    #[Route('/{id}/lend', name: 'admin_task_lend', methods: ['POST'])]
    public function lend(Task $task): Response
    {
        $this->taskService->lendTask($task);

        return $this->redirectToRoute('admin_task_index');
    }

    /**
     * Return a task.
     *
     * @Route("/{id}/return", name: "admin_task_return", methods: ["POST"])
     *
     * @param  Task $task The task entity to approve
     *
     * @return Response
     */
    #[Route('/{id}/return', name: 'admin_task_return', methods: ['POST'])]
    public function return(Task $task): Response
    {
        $this->taskService->returnTask($task);

        return $this->redirectToRoute('admin_task_index');
    }

    /**
     * Display task details.
     *
     * @Route("/{id}/details", name: "admin_task_details", methods: ["GET"])
     *
     * @param  Task $task The task entity to approve
     *
     * @return Response
     */
    #[Route('/{id}/details', name: 'admin_task_details', methods: ['GET'])]
    public function details(Task $task): Response
    {
        return $this->render('admin_task/details.html.twig', [
            'task' => $task,
        ]);
    }
}

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
    public function __construct(private readonly TaskServiceInterface $taskService)
    {
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
            // Tutaj możesz przetworzyć dane z formularza, na przykład wysłać e-mail z potwierdzeniem rezerwacji
            // $data = $form->getData();

            $this->addFlash('success', 'Reservation has been made successfully.');

            return $this->redirectToRoute('public_task_show', ['id' => $task->getId()]);
        }

        return $this->render('public_task/reserve.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}
<?php

/**
 * Task controller.
 */

namespace App\Controller;

use App\Dto\TaskListInputFiltersDto;
use App\Entity\Task;
use App\Entity\User;
use App\Form\Type\TaskType;
use App\Resolver\TaskListInputFiltersDtoResolver;
use App\Service\TaskServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaskController.
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService Task service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly TaskServiceInterface $taskService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param TaskListInputFiltersDto $filters Input filters DTO
     * @param int                     $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'task_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryString(resolver: TaskListInputFiltersDtoResolver::class)] TaskListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->taskService->getPaginatedList(
            $page,
            $user,
            $filters
        );

        return $this->render('task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'task_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW', subject: 'task')]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'task_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = new Task();
        $task->setAuthor($user);
        $form = $this->createForm(
            TaskType::class,
            $task,
            ['action' => $this->generateUrl('task_create')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'task_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'task')]
    public function edit(Request $request, Task $task): Response
    {
        $user = $this->getUser();

        if ($task->getAuthor() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.record_not_found')
            );

            return $this->redirectToRoute('task_index');
        }

        $form = $this->createForm(
            TaskType::class,
            $task,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('task_edit', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'task_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'task')]
    public function delete(Request $request, Task $task): Response
    {
        $user = $this->getUser();

        if ($task->getAuthor() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.record_not_found')
            );

            return $this->redirectToRoute('task_index');
        }

        $form = $this->createForm(
            FormType::class,
            $task,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('task_delete', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->delete($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }
}

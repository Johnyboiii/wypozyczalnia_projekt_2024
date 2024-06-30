<?php

/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\User\UserEditType;
use App\Form\Type\User\UserRoleType;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * UserController class.
 *
 * @IsGranted('ROLE_ADMIN')
 */
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private UserServiceInterface $userService;

    /**
     * UserReservationService constructor.
     *
     * @param UserServiceInterface $userService The user service responsible for user-related operations
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Index action.
     *
     * @Route('/users', name: 'user_index', methods: ['GET'])
     *
     * @return Response
     */
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userService->findAllUsers();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * New action.
     *
     * @Route('/users/new', name: 'user_new', methods: ['GET', 'POST'])
     *
     * @param Request                     $request            HTTP request
     * @param UserPasswordHasherInterface $userPasswordHasher Password hasher
     *
     * @return Response HTTP response
     */
    #[Route('/users/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $this->userService->saveUser($user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUser($user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit role action.
     *
     * @Route('/users/{id}/edit-role', name: 'user_edit_role', methods: ['GET', 'POST'])
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/users/{id}/edit-role', name: 'user_edit_role', methods: ['GET', 'POST'])]
    public function editRole(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUser($user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit_role.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

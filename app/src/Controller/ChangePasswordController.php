<?php

/**
 * ChangePassword controller.
 */

namespace App\Controller;

use App\Form\Type\ChangePasswordType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Controller for changing password.
 */
class ChangePasswordController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    private UserService $userService;

    /**
     * ChangePasswordController constructor.
     *
     * @param TokenStorageInterface $tokenStorage The token storage service
     * @param UserService           $userService  The user service
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserService $userService)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userService = $userService;
    }

    /**
     * Change the password.
     *
     * @Route("/change-password", name="app_change_password")
     *
     * @param Request                     $request        The request object
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service
     *
     * @return Response The response object
     *
     * @throws \Exception
     */
    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof PasswordAuthenticatedUserInterface) {
                throw new \Exception('The user is not authenticated.');
            }

            if (!$user instanceof \App\Entity\User) {
                throw new \Exception('The user is not of the correct class.');
            }

            $oldPassword = $form->get('oldPassword')->getData();
            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $newPassword = $form->get('newPassword')->getData();
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $this->userService->save($user); // Use the UserService to save the user
                $this->addFlash('success', 'Your password has been changed.');
                $this->tokenStorage->setToken(null);
                $request->getSession()->invalidate();

                return $this->redirectToRoute('app_login');
            }

            $form->addError(new FormError('Invalid old password.'));
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

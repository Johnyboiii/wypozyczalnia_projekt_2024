<?php

// src/Controller/ChangePasswordController.php
namespace App\Controller;

use App\Form\Type\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller for changing password.
 */
class ChangePasswordController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;


    /**
     * ChangePasswordController constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Change the password.
     *
     * @Route("/change-password", name="app_change_password")
     *
     * @param Request                     $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface      $entityManager
     *
     * @return Response
     *
     * @throws \Exception
     */
    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
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
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Your password has been changed.');
                $this->tokenStorage->setToken(null);
                $request->getSession()->invalidate();

                return $this->redirectToRoute('app_login');
            } else {
                $form->addError(new FormError('Invalid old password.'));
            }
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

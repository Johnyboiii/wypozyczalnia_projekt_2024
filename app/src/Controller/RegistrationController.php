<?php

/**
 * RegistrationController.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for user registration.
 */
class RegistrationController extends AbstractController
{
    private UserServiceInterface $userService;

    /**
     * RegistrationController constructor.
     *
     * @param UserServiceInterface $userService The user service
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Handles user registration.
     *
     * @param Request                     $request            The current request
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher
     * @param Security                    $security           The security component
     *
     * @return Response The response
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Register the user using UserService
            $plainPassword = $form->get('plainPassword')->getData();
            $registeredUser = $this->userService->register($user, $plainPassword);

            // Log in the user after successful registration
            return $security->login($registeredUser, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

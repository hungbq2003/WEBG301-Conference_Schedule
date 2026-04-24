<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $formData = [
            'firstName' => '',
            'lastName' => '',
            'email' => '',
        ];

        if ($request->isMethod('POST')) {
            $formData = [
                'firstName' => trim((string) $request->request->get('firstName', '')),
                'lastName' => trim((string) $request->request->get('lastName', '')),
                'email' => trim((string) $request->request->get('email', '')),
            ];
            $password = (string) $request->request->get('password', '');
            $confirmation = (string) $request->request->get('confirmPassword', '');

            if (empty($formData['firstName']) || empty($formData['lastName']) || empty($formData['email']) || empty($password)) {
                $this->addFlash('danger', 'Please complete all required registration fields.');
            } elseif ($password !== $confirmation) {
                $this->addFlash('danger', 'Password confirmation does not match.');
            } elseif ($userRepository->findOneBy(['email' => $formData['email']])) {
                $this->addFlash('danger', 'An account with that email already exists.');
            } else {
                $user = new User();
                $user->setFirstName($formData['firstName']);
                $user->setLastName($formData['lastName']);
                $user->setEmail($formData['email']);
                $user->setPassword($passwordHasher->hashPassword($user, $password));

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Registration complete. You are now logged in.');

                return $userAuthenticator->authenticateUser($user, $authenticator, $request);
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $formData,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/account', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function account(): Response
    {
        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}

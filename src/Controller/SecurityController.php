<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // recupere l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash('error', "L'email ou le mot de passe est incorrect.");
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifier si l'utilisateur est bloqué
        if ($lastUsername) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $lastUsername]);

            if ($user && $user->isStatut()) {

                $user->setRoles(['ROLE_BLOCKED']);

                $this->entityManager->flush();

                $this->addFlash('error', "Vous êtes bloqué, veuillez contacter le support.");
                return $this->redirectToRoute('app_blocked');
            }
        }

        return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
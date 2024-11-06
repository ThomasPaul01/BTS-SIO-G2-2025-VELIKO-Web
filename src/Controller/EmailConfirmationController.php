<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EmailConfirmationController extends AbstractController
{
    #[Route('/confirm-email/{token}', name: 'app_confirm_email')]
    public function confirmEmail(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {

        // Récupérer les données utilisateur depuis la session
        $userData = $session->get('user_data');

        // Vérifier que le token correspond
        if ($userData && $userData['confirmationToken'] === $token) {
            $user = new User();

            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setPassword($userData['password']);
            $user->setName($userData['name']);
            $user->setFirstName($userData['firstName']);
            $user->setBirthdate($userData['birthdate']);

            $user->setAddress($userData['address']);
            $user->setPostalCode($userData['postalCode']);
            $user->setCity($userData['city']);

            $user->setVerified(true);
            $user->setConfirmationToken(null);

            // Persister l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Supprimer les données de session
            $session->remove('user_data');

            $this->addFlash('success', 'Votre adresse e-mail a été confirmée. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('error', 'Le lien de confirmation est invalide ou expiré.');
        return $this->redirectToRoute('app_register');
    }
}

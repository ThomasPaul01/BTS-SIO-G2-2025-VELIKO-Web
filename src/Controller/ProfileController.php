<?php

namespace App\Controller;

use App\Form\ProfileFormType; // Assurez-vous d'inclure le bon namespace du form type
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $security->getUser();

        // Si aucun utilisateur n'est connecté, on peut rediriger vers la page de connexion
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Crée un formulaire basé sur l'entité User
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on enregistre les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Les informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'profileForm' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordController extends AbstractController
{
    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $security->getUser();

        // Crée le formulaire pour changer le mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);

        // Gère la soumission du formulaire
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les mots de passe du formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérifie si les deux mots de passe correspondent
            if ($plainPassword !== $confirmPassword) {
                // Ajoute un message d'erreur si les mots de passe ne correspondent pas
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');

                // Renvoie à la vue avec les messages d'erreur
                return $this->render('password/change_password.html.twig', [
                    'changePasswordForm' => $form->createView(),
                ]);
            }

            // Si les mots de passe correspondent, hache le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');

            // Redirige vers le profil ou la page souhaitée
            return $this->redirectToRoute('app_profile');
        }

        // Rends la vue avec le formulaire
        return $this->render('password/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}

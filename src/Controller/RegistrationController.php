<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setRoles(['ROLE_USER']);

            if(!$this->verifierMotDePasse($plainPassword)) {

                // erreur
                $form->get('plainPassword')->addError(new FormError('Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'));

                // Recharger la page d'enregistrement sans rediriger
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);

    }
    function verifierMotDePasse($motDePasse): bool
    {

        // minimum 12 caractères
        if (strlen($motDePasse) < 12) {
            return false;
        }

        // au moins une lettre minuscule
        if (!preg_match('/[a-z]/', $motDePasse)) {
            return false;
        }

        // au moins une lettre majuscule
        if (!preg_match('/[A-Z]/', $motDePasse)) {
            return false;
        }

        // au moins un chiffre
        if (!preg_match('/[0-9]/', $motDePasse)) {
            return false;
        }
        // eu moins un caractère spécial
        $caracteresSpeciaux = '@$!%*?&';
        $trouveCaractereSpecial = false;
        for ($i = 0; $i < strlen($motDePasse); $i++) {
            if (strpos($caracteresSpeciaux, $motDePasse[$i]) !== false) {
                $trouveCaractereSpecial = true;
                break;
            }
        }
        if (!$trouveCaractereSpecial) {
            return false;
        }

        // le mot de passe est valide
        return true;

    }

}

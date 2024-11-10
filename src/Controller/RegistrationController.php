<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Services\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Token $tokenService,
        SessionInterface $session

    ): Response {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérifiez si les mots de passe correspondent
            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                return $this->render('registration/index.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            // Vérifiez la validité du mot de passe
            if (!$this->verifierMotDePasse($plainPassword)) {
                $form->get('plainPassword')->addError(new FormError('Le mot de passe doit respecter les critères de sécurité.'));
                return $this->render('registration/index.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $user->setVerified(false);

            // Générez le token et l'enregistrez pour l'utilisateur
            $confirmationToken = $tokenService->generateToken();
            $user->setConfirmationToken($confirmationToken);

            // Stocker les données utilisateur dans la session temporairement
            $session->set('user_data', [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                "roles" => $user->getRoles(),
                'password' => $user->getPassword(),
                'name' => $user->getName(),
                "firstName" => $user->getFirstName(),
                "birthdate" => $user->getBirthdate(),
                "address" => $user->getAddress(),
                "postalCode" => $user->getPostalCode(),
                "city" => $user->getCity(),
                'confirmationToken' => $confirmationToken,
            ]);

            // Créez le lien de confirmation
            $confirmationUrl = $this->generateUrl(
                'app_confirm_email',
                ['token' => $confirmationToken],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            // Envoyez l'e-mail de confirmation
            $email = (new Email())
                ->from('noreply@yourdomain.com')
                ->to($user->getEmail())
                ->subject('Confirmation d\'adresse e-mail')
                ->html("<p>Veuillez cliquer sur le lien pour confirmer votre adresse e-mail : <a href='{$confirmationUrl}'>Confirmer mon adresse e-mail</a></p>");

            $mailer->send($email);

            $this->addFlash('success', 'Un e-mail de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
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
        // au moins un caractère spécial
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
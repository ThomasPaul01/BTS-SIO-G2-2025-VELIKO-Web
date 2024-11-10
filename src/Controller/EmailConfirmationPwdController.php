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

class EmailConfirmationPwdController extends AbstractController
{
    #[Route('/confirm-email/{token}', name: 'app_confirm_email_pwd')]
    public function confirmEmail(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {

        return $this->redirectToRoute('app_change_password');
    }
}
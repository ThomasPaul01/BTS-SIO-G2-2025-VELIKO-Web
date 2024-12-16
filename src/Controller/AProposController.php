<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AProposController extends AbstractController
{
    #[Route('/a_propos', name: 'app_a_propos')]
    public function index(): Response
    {
        $user = $this->getUser();
        // Vérifiez si l'utilisateur est connecté et si le changement de mot de passe est requis
        if ($user && $user->isMustChangePassword())
        {
            return $this->redirectToRoute('app_emailMdp');
        }
        return $this->render('a_propos/index.html.twig', [
            'controller_name' => 'AProposController',
        ]);
    }
}
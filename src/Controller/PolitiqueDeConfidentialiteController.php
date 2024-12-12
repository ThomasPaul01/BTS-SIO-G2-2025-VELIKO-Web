<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PolitiqueDeConfidentialiteController extends AbstractController
{
    #[Route('/politique_confidentialite', name: 'app_politique_de_confidentialite')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user && $user->isMustChangePassword())
        {
            return $this->redirectToRoute('app_emailMdp');
        }

        return $this->render('politique_de_confidentialite/index.html.twig', [
            'controller_name' => 'PolitiqueDeConfidentialiteController',
        ]);
    }
}
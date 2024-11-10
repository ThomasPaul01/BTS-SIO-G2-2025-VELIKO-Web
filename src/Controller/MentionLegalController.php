<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionLegalController extends AbstractController
{
    #[Route('/mention/legal', name: 'app_mention_legal')]
    public function index(): Response
    {
        return $this->render('mention_legal/index.html.twig', [
            'controller_name' => 'MentionLegalController',
        ]);
    }
}
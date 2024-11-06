<?php

namespace App\Controller;

use App\Entity\StationFav;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteUserController extends AbstractController
{
    #[Route('/user/profile/delete', name: 'app_delete_user', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Affichage de la page de confirmation si la méthode est GET
        if ($request->isMethod('GET')) {
            return $this->render('deleteUser/index.html.twig', [
                'user' => $user,
            ]);

        }

        // Vérification de la requête POST
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {


                // Récupérer toutes les entités StationFav associées à l'utilisateur
                $favorites = $entityManager->getRepository(StationFav::class)->findBy(['userEmail' => $user->getUserIdentifier()]);
                //Et les supprimer
                foreach ($favorites as $favorite) {
                    $entityManager->remove($favorite);
                }

                // Anonymiser les données utilisateur
                $user->setEmail('Anonyme['. uniqid().']');
                $user->setName('Anonymisé');
                $user->setFirstName('Anonymisé');
                $user->setAddress('Anonymisée');


                $entityManager->flush();

                // Rediriger après la suppression
                return $this->redirectToRoute('app_logout');


            }
        }

        // Dans le cas où la méthode est GET, nous affichons le formulaire
        return $this->render('deleteUser/index.html.twig', [
            'user' => $user,
        ]);
    }
}

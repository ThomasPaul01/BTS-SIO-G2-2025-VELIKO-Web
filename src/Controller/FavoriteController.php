<?php
namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationFav;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class FavoriteController extends AbstractController
{
    #[Route('/user/favorite', name: 'app_favorite')]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $user->isMustChangePassword())
        {
            return $this->redirectToRoute('app_emailMdp');
        }

        // Récupérer les favoris de l'utilisateur
        $favorites = $entityManager->getRepository(StationFav::class)
            ->findBy(['userEmail' => $user->getUserIdentifier()]);

        $stations = [];
        foreach ($favorites as $favorite) {
            $station = $entityManager->getRepository(Station::class)
                ->find($favorite->getStationId());

            if ($station) {
                //remplissage Tableau
                $stations[] = $station;
            }
        }

        return $this->render('favorite/index.html.twig', [
            'stations' => $stations,
        ]);
    }

    #[Route('/user/add-favorite/{stationId}', name: 'add_favorite')]
    public function addFavorite(int $stationId, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        $existingFavorite = $entityManager->getRepository(StationFav::class)->findOneBy([
            'station_id' => $stationId,
            'userEmail' => $user->getUserIdentifier()
        ]);

        if ($existingFavorite) {
            return new JsonResponse(['message' => 'Station déjà dans les favoris'], 400);
        }

        // Ajouter une nouvelle station favorite dans la BDD avec l'email de l'utilisateur
        $favorite = new StationFav();
        $favorite->setUserEmail($user->getUserIdentifier());
        $favorite->setStationId($stationId);

        $entityManager->persist($favorite);

        try {
            $entityManager->flush();
            return new JsonResponse(['message' => 'Station ajoutée aux favoris', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de l\'ajout aux favoris: ' . $e->getMessage(), 'type' => 'error'], 500);
        }
    }

    #[Route('/user/remove-favorite/{stationId}', name: 'remove_favorite', methods: ['POST'])]
    public function removeFavorite(int $stationId, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $favorite = $entityManager->getRepository(StationFav::class)->findOneBy([
            'station_id' => $stationId,
            'userEmail' => $user->getUserIdentifier()
        ]);

        if ($favorite) {
            $entityManager->remove($favorite);
        }

        try {
            $entityManager->flush();
            return new JsonResponse(['message' => 'Station retirée des favoris.', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression des favoris: ' . $e->getMessage(), 'type' => 'error'], 500);
        }
    }
}
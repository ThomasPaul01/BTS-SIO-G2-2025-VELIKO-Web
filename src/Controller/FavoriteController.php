<?php
namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationFav;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class FavoriteController extends AbstractController
{
    #[Route('/favorite', name: 'app_favorite')]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

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

    #[Route('/remove-favorite/{stationId}', name: 'remove_favorite', methods: ['POST'])]
    public function removeFavorite(int $stationId, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $favorite = $entityManager->getRepository(StationFav::class)->findOneBy([
            'station_id' => $stationId,
            'userEmail' => $user->getUserIdentifier()
        ]);

        if ($favorite) {
            $entityManager->remove($favorite);
            $entityManager->flush();

            $this->addFlash('success', 'Station retirée des favoris.');
        } else {
            $this->addFlash('error', 'Station non trouvée dans vos favoris.');
        }

        return $this->redirectToRoute('app_favorite');
    }
}

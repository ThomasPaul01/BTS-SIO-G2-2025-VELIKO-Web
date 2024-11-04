<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationFav;
use App\Request\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    #[Route('/map', name: 'fetchVelikoData')]
    public function fetchVelikoData(EntityManagerInterface $entityManager): Response
    {


        $stationUrl = '/api/stations';
        $statusUrl = '/api/stations/status';

        try {
            // Request Api
            $stations = $this->request->RequestApi($stationUrl);
            $stationStatuses = $this->request->RequestApi($statusUrl);

        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel à l'API: " . $e->getMessage(), 500);
        }

        foreach ($stations as $station) {
            $stationId = $station['station_id'];
            foreach ($stationStatuses as $stationStatus) {
                if ($stationId == $stationStatus['station_id']) {
                    // Vérifiez si la station existe déjà dans la base de données
                    $existingStation = $entityManager->getRepository(Station::class)->findOneBy(['station_id' => $stationId]);

                    if (!$existingStation) {
                        $stationEntity = new Station();
                        $stationEntity->setStationId($stationId);
                        $stationEntity->setName($station['name']);
                        $stationEntity->setCapacity($station['capacity']);
                        $stationEntity->setLat($station['lat']);
                        $stationEntity->setLon($station['lon']);
                        $stationEntity->setMechanicalBikes($stationStatus['num_bikes_available_types'][0]['mechanical'] ?? 0);
                        $stationEntity->setElectricBikes($stationStatus['num_bikes_available_types'][1]['ebike'] ?? 0);

                        $entityManager->persist($stationEntity);
                    } else {
                        // Vous pouvez mettre à jour les informations si la station existe déjà
                        $existingStation->setCapacity($station['capacity']);
                        $existingStation->setLat($station['lat']);
                        $existingStation->setLon($station['lon']);
                        $existingStation->setMechanicalBikes($stationStatus['num_bikes_available_types'][0]['mechanical'] ?? 0);
                        $existingStation->setElectricBikes($stationStatus['num_bikes_available_types'][1]['ebike'] ?? 0);
                    }

                    break;
                }
            }
        }

        $entityManager->flush();

        // Récupérer toutes les stations de la base de données
        $allStations = $entityManager->getRepository(Station::class)->findAll();

        // Créer le tableau de données à envoyer au template
        $stationsData = [];
        foreach ($allStations as $station) {
            $stationsData[] = [
                'station_id' => $station->getStationId(),
                'name' => $station->getName(),
                'capacity' => $station->getCapacity(),
                'lat' => $station->getLat(),
                'lon' => $station->getLon(),
                'mechanical_bikes' => $station->getMechanicalBikes(),
                'electric_bikes' => $station->getElectricBikes(),
            ];
        }

        // Récupérer l'email de l'utilisateur
        $user = $this->getUser();
        $userEmail = $user?->getUserIdentifier();


        return $this->render('api/index.html.twig', [
            'stations' => $stationsData,
            'userEmail' => $userEmail,
        ]);
    }


    #[Route('/add-favorite/{stationId}', name: 'add_favorite')]
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

}

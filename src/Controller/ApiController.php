<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationFav;
use App\Request\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    #[Route('/', name: 'fetchVelikoData')]
    public function fetchVelikoData(EntityManagerInterface $entityManager): Response
    {
        $lastUpdatedStation = $entityManager->getRepository(Station::class)->findOneBy([], ['lastUpdatedAt' => 'DESC']);

        // Vérifiez si les données ont été mises à jour aujourd'hui
        if ($lastUpdatedStation && $lastUpdatedStation->getLastUpdatedAt() && $lastUpdatedStation->getLastUpdatedAt()->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {
            return $this->redirectToRoute('app_login');

        }

        // Si les données n'ont pas été mises à jour aujourd'hui, récupérer les nouvelles données depuis l'API

        $stationUrl = '/api/stations';
        $statusUrl = '/api/stations/status';

        try {
            $stations = $this->request->RequestApi($stationUrl);
            $stationStatuses = $this->request->RequestApi($statusUrl);
        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel à l'API: " . $e->getMessage(), 500);
        }

        foreach ($stations as $station) {
            $stationId = $station['station_id'];
            foreach ($stationStatuses as $stationStatus) {
                if ($stationId == $stationStatus['station_id']) {
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
                        $stationEntity->setLastUpdatedAt(new \DateTime());

                        $entityManager->persist($stationEntity);
                    } else {
                        $existingStation->setCapacity($station['capacity']);
                        $existingStation->setLat($station['lat']);
                        $existingStation->setLon($station['lon']);
                        $existingStation->setMechanicalBikes($stationStatus['num_bikes_available_types'][0]['mechanical'] ?? 0);
                        $existingStation->setElectricBikes($stationStatus['num_bikes_available_types'][1]['ebike'] ?? 0);
                        $existingStation->setLastUpdatedAt(new \DateTime());
                    }
                    break;
                }
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_login');

    }

// Méthode pour rendre les données de la carte sans recharger l'API
    #[Route('/user/map', name: 'initMap')]
    public function renderStations(EntityManagerInterface $entityManager): Response
    {
        $allStations = $entityManager->getRepository(Station::class)->findAll();

        $user = $this->getUser();
        $userEmail = $user?->getUserIdentifier();
        $favorites = $entityManager->getRepository(StationFav::class)->findBy(['userEmail' => $userEmail]);
        $favoriteStationIds = array_map(fn($fav) => $fav->getStationId(), $favorites);

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
                'is_favorite' => in_array($station->getStationId(), $favoriteStationIds),
            ];
        }

        return $this->render('map/index.html.twig', [
            'stations' => $stationsData,
            'userEmail' => $userEmail,
        ]);
    }

}

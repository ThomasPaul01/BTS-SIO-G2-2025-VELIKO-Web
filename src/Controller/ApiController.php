<?php
// src/Controller/ApiController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Station;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    private $client;
    private $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/velib", name="fetch_velib_data")
     * @throws TransportExceptionInterface
     */
    public function fetchVelibData(): Response
    {
        // Récupération des informations des stations (nom, coordonnées, etc.)
        $stationInfoResponse = $this->client->request('GET', 'https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json');
        // Récupération du statut des stations (vélos disponibles)
        $stationStatusResponse = $this->client->request('GET', 'https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json');

        if ($stationInfoResponse->getStatusCode() !== 200 || $stationStatusResponse->getStatusCode() !== 200) {
            return new Response('Error while fetching data.');
        }

        $stationInfoData = $stationInfoResponse->toArray();
        $stationStatusData = $stationStatusResponse->toArray();

        $stationStatuses = [];
        foreach ($stationStatusData['data']['stations'] as $status) {
            $stationStatuses[$status['station_id']] = $status;
        }

        // Traitement des stations
        foreach ($stationInfoData["data"]["stations"] as $station) {
            $stationInDB = $this->entityManager->getRepository(Station::class)
                ->findOneBy(['station_id' => $station["station_id"]]);

            $status = $stationStatuses[$station["station_id"]] ?? null;

            if (!$stationInDB) {
                $newStation = new Station();
                $newStation->setStationId($station["station_id"]);
                $newStation->setName($station["name"]);
                $newStation->setLatitude($station["lat"]);
                $newStation->setLongitude($station["lon"]);
                $newStation->setCapacity($station["capacity"]);

                if ($status) {
                    $newStation->setNumBikesAvailable($status["num_bikes_available"]);
                    $newStation->setNumBikesAvailableMechanical($status["num_bikes_available_types"][0]["mechanical"] ?? 0);
                    $newStation->setNumBikesAvailableElectric($status["num_bikes_available_types"][1]["ebike"] ?? 0);
                }

                $this->entityManager->persist($newStation);
            } else {
                // La station existe déjà, on la met à jour
                $stationInDB->setName($station["name"]);
                $stationInDB->setLatitude($station["lat"]);
                $stationInDB->setLongitude($station["lon"]);
                $stationInDB->setCapacity($station["capacity"]);
                if ($status) {
                    $stationInDB->setNumBikesAvailable($status["num_bikes_available"]);
                    $stationInDB->setNumBikesAvailableMechanical($status["num_bikes_available_types"][0]["mechanical"] ?? 0);
                    $stationInDB->setNumBikesAvailableElectric($status["num_bikes_available_types"][1]["ebike"] ?? 0);
                }

                $this->entityManager->persist($stationInDB);
            }
        }

        $this->entityManager->flush();

        return $this->render('api/index.html.twig', [
            'stations' => $stationInfoData['data']['stations'],
            'statuses' => $stationStatuses,
        ]);
    }
}

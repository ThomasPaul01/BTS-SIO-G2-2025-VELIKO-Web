<?php
// src/Controller/ApiController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Station;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

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

        $response = $this->client->request('GET', 'https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json');

        if ($response->getStatusCode() !== 200) {
            return new Response('Error while fetching data: ' . $response->getStatusCode());
        }

        $data = $response->toArray();

        // Traitement des stations comme avant
        foreach ($data["data"]["stations"] as $station) {
            $stationInDB = $this->entityManager->getRepository(Station::class)
                ->findOneBy(['station_id' => $station["station_id"]]);

            if (!$stationInDB) {

                $newStation = new Station();
                $newStation->setStationId($station["station_id"]);
                $newStation->setName($station["name"]);
                $newStation->setLatitude($station["lat"]);
                $newStation->setLongitude($station["lon"]);
                $newStation->setCapacity($station["capacity"]);

                $this->entityManager->persist($newStation);
            } else {
                // La station existe déjà, on la met à jour
                $stationInDB->setName($station["name"]);
                $stationInDB->setLatitude($station["lat"]);
                $stationInDB->setLongitude($station["lon"]);
                $stationInDB->setCapacity($station["capacity"]);

                $this->entityManager->persist($stationInDB);
            }
        }


        $this->entityManager->flush();
        return $this->render('api/index.html.twig'  , [
            'stations' => $data['data']['stations'],
        ]);


    }
}

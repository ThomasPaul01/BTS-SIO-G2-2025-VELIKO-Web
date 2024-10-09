<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Request;
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

    #[Route('/map', name: 'fetchVelikoData')]
    public function fetchVelikoData(): Response
    {
        $url = 'http://localhost:9042/api/stations';
        $statusUrl = 'http://localhost:9042/api/stations/status';

        try {
            // Request Api
            $stations = $this->request->RequestApi($url);
            $stationStatuses = $this->request->RequestApi($statusUrl);
        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel à l'API: " . $e->getMessage(), 500);
        }

        $stationsWithStatuses = [];
        foreach ($stations as $station) {
            $stationId = $station['station_id'];
            foreach ($stationStatuses as $stationStatus) {
                if($stationId == $stationStatus['station_id']) {

                    // Récupération des informations de la station
                    $stationData = [
                        'name' => $station['name'],
                        'capacity' => $station['capacity'],
                        'lat' => $station['lat'],
                        'lon' => $station['lon'],
                        'mechanical_bikes' => $stationStatus['num_bikes_available_types'][0]['mechanical'] ?? 0,
                        'electric_bikes' => $stationStatus['num_bikes_available_types'][1]['ebike'] ?? 0,
                    ];
                    //dump($stationData);
                    $stationsWithStatuses[] = $stationData;

                    break;
                }
            }

        }

        //recover the id
        $user = $this->getUser();
        $userEmail = null;
        if ($user) {
            $userEmail = $user->getUserIdentifier();
        }

        return $this->render('api/index.html.twig', [
            'stations' => $stationsWithStatuses,
            'userEmail' => $userEmail,
        ]);
    }
}

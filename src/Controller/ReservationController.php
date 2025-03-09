<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Station;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReservationController extends AbstractController
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    #[Route('/user/reservation/{idStationDepart}', name: 'app_reservation', requirements: ['idStationDepart' => '\d+'])]
    public function index(int $idStationDepart, EntityManagerInterface $entityManager): Response
    {
        $stationDepart = $entityManager->getRepository(Station::class)->find($idStationDepart);

        $stations = $entityManager->getRepository(Station::class)->findAll();
        $stationsData = array_map(function ($station) {
            return [
                'station_id' => $station->getStationId(),
                'name' => $station->getName(),
            ];
        }, $stations);

        return $this->render('reservation/index.html.twig', [
            'station_depart' => $stationDepart,
            'stations' => $stationsData,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/user/reservation/validate', name: 'app_reservation_validate', methods: ['POST'])]
    public function validate(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $veloFound = false;

        // Vérifier si la requête est de type POST
        if ($request->isMethod('POST')) {
            $stationDepartId = $request->request->get('station_depart_id');
            $stationArrivee = $request->request->get('station_arrivee_id');
            // Récupérer les stations disponibles dans l'API
            $stations = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/api/stations");
            $stations = $stations->toArray();
            $typeVelo = $request->request->get('type_velo');
            $velos = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/api/velos");
            $velos = $velos->toArray();
            $auth_token = json_decode(file_get_contents("../var/api/configDataset.json"), true)['token']['default']; // Récupérer le token d'authentification

            // Parcourir les vélos pour trouver un vélo disponible
            foreach ($velos as $velo) {
                if ($velo['station_id_available'] == $stationDepartId) {
                    if ($velo['status'] == "available" ) {

                        /*if ($velo['type'] != $typeVelo) {
                            continue;
                        }
                        else {*/

                        // Réserver le vélo
                        $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/api/velo/" . $velo['velo_id'] . "/location", [
                            'headers' => ["Authorization" => $auth_token]
                        ]);

                        // Mettre à jour le nombre de vélos disponibles à la station de départ
                        $stationDepart = $entityManager->getRepository(Station::class)->find($stationDepartId);
                        if ($typeVelo == 'electric') {
                            $stationDepart->setElectricBikes($stationDepart->getElectricBikes() - 1);
                        } else {
                            $stationDepart->setMechanicalBikes($stationDepart->getMechanicalBikes() - 1);
                        }
                        $entityManager->persist($stationDepart);
                        $entityManager->flush();


                        $veloFound = true;

                        $this->addFlash('success', "Votre réservation a été effectuée avec succès");
                        // Restaurer le vélo à la station de fin
                        $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/api/velo/" . $velo['velo_id'] . "/restore/" . $stationArrivee, [
                            'headers' => ["Authorization" => $auth_token],
                        ]);
                        break;
                    }

                    else {
                        // Dans le cas où aucun vélo n'est disponible dans la station
                        /** @var StationRepository $stationRepository */
                        $stationRepository = $this->entityManager->getRepository(Station::class);
                        $this->addFlash("danger", "Aucun vélo n'est disponible dans la station " . $stationRepository->getStationNameById($velo['station_id_available'])[0]["name"]);

                        return $this->redirectToRoute('app_reservation', [
                            'idStationDepart' => $stationDepartId
                        ]);
                    }
                }
            }
        }

        // Vérifier si la station d'arrivée existe dans les stations disponibles
        $stationArriveeExist = false;

        foreach ($stations as $station) {
            if ($station['station_id'] == $stationArrivee) {
                $stationArriveeExist = true;
                break;
            }
        }

        // Si la station d'arrivée ne fait pas partie des stations de l'API, envoyer une alerte
        if (!$stationArriveeExist) {
            $this->addFlash('error', "La station d'arrivée sélectionnée ne fait pas partie des stations disponibles.");
            return $this->redirectToRoute('app_reservation', [
                'idStationDepart' => $stationDepartId
            ]);
        }

        //date actuelle
        $currentDate = new \DateTime();

        $user = $security->getUser();

        //Creation reservation
        $reservation = new Reservation();
        $reservation->setDateReservation($currentDate);
        $reservation->setIdStationDepart((int)$stationDepartId);
        $reservation->setIdStationFin((int)$stationArrivee);
        $reservation->setUserEmail($user->getUserIdentifier());
        $reservation->setTypeVelo($typeVelo);

        $entityManager->persist($reservation);

        try {
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a bien été effectuée.');
            return $this->redirectToRoute('initMap');

        } catch (\Exception $e) {
            $this->addFlash('error', "Une erreur c'est produite lors de l'enregistrement de votre reservation. Veuillez contacter un administrateur.");

            return $this->redirectToRoute('app_reservation', [
                'idStationDepart' => $stationDepartId
            ]);
        }
    }
}
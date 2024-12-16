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

class ReservationController extends AbstractController
{
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
    public function validate(Request $request,EntityManagerInterface $entityManager,Security $security): Response
    {
        // Récupérer les données du formulaire
        $stationDepartId = $request->request->get('station_depart_id');
        $stationArrivee = $request->request->get('station_arrivee_id');
        $typeVelo = $request->request->get('type_velo');

        //date actuelle
        $currentDate = new \DateTime();

        $user=$security->getUser();

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
            $this->addFlash('success','La réservation a bien été effectuée.');
            return $this->redirectToRoute('initMap');

        } catch (\Exception $e) {
            $this->addFlash('error', "Une erreur c'est produite lors de l'enregistrement de votre reservation. Veuillez contacter un administrateur.");

            return $this->redirectToRoute('app_reservation',[
                'idStationDepart' => $stationDepartId
            ]);
        }

    }
}

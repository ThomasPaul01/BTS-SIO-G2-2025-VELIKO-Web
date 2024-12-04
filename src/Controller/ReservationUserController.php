<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\StationFav;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationUserController extends AbstractController
{
    #[Route('user/reservation', name: 'app_reservation_user')]
    public function index(EntityManagerInterface $entityManager, StationRepository $stationRepository, Security $security): Response {

        $user = $security->getUser();

        // Récupérer les réservations de l'utilisateur
        $reservations = $entityManager->getRepository(Reservation::class)
            ->findBy(['userEmail' => $user->getUserIdentifier()]);

        // Récupérer les noms des stations associés aux IDs
        $stations = [];
        foreach ($reservations as $reservation) {
            $stations[$reservation->getIdStationDepart()] = $stationRepository->find($reservation->getIdStationDepart());
            $stations[$reservation->getIdStationFin()] = $stationRepository->find($reservation->getIdStationFin());
        }

        return $this->render('reservation_user/index.html.twig', [
            'reservations' => $reservations,
            'stations' => $stations,
        ]);
    }
    #[Route('user/reservation/remove-{idReservation}', name: 'remove_reservation_user', methods: ['POST'])]
    public function removeReservation(int $idReservation, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        // Rechercher la réservation pour l'utilisateur actuel
        $reservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'id' => $idReservation,
            'userEmail' => $user->getUserIdentifier()
        ]);

        if (!$reservation) {
            return new JsonResponse(['message' => 'Réservation introuvable ou non autorisée.', 'type' => 'error'], 404);
        }

        // Supprimer la réservation
        $entityManager->remove($reservation);

        try {
            $entityManager->flush();
            return new JsonResponse(['message' => 'Réservation supprimée avec succès.', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression : ' . $e->getMessage(), 'type' => 'error'], 500);
        }
    }
}

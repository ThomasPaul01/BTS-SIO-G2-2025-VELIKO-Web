<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_reservation = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $id_station_depart = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $id_station_fin = null;

    #[ORM\Column]
    private ?int $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(\DateTimeInterface $date_reservation): static
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getIdStationDepart(): ?string
    {
        return $this->id_station_depart;
    }

    public function setIdStationDepart(string $id_station_depart): static
    {
        $this->id_station_depart = $id_station_depart;

        return $this;
    }

    public function getIdStationFin(): ?string
    {
        return $this->id_station_fin;
    }

    public function setIdStationFin(string $id_station_fin): static
    {
        $this->id_station_fin = $id_station_fin;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}

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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_reservation = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id_station_depart = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id_station_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $type_velo = null;

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

    public function getIdStationDepart(): ?int
    {
        return $this->id_station_depart;
    }

    public function setIdStationDepart(int $id_station_depart): static
    {
        $this->id_station_depart = $id_station_depart;

        return $this;
    }

    public function getIdStationFin(): ?int
    {
        return $this->id_station_fin;
    }

    public function setIdStationFin(int $id_station_fin): static
    {
        $this->id_station_fin = $id_station_fin;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): static
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getTypeVelo(): ?string
    {
        return $this->type_velo;
    }

    public function setTypeVelo(string $type_velo): static
    {
        $this->type_velo = $type_velo;

        return $this;
    }
}

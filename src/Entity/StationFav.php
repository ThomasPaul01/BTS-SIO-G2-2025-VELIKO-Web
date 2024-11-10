<?php

namespace App\Entity;

use App\Repository\StationFavRepository;
use Cassandra\Bigint;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationFavRepository::class)]
class StationFav
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $station_id = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStationId(): ?int
    {
        return $this->station_id;
    }

    public function setStationId(int $station_id): static
    {
        $this->station_id = $station_id;

        return $this;
    }
}
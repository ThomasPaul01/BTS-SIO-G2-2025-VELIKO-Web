<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private ?int $station_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?float $lat = null;

    #[ORM\Column]
    private ?float $lon = null;

    #[ORM\Column]
    private ?int $mechanical_bikes = null;

    #[ORM\Column]
    private ?int $electric_bikes = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastUpdatedAt = null;

    // Getters et Setters...

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStationId(): ?int
    {
        return $this->station_id;
    }

    public function setStationId(?int $station_id): void
    {
        $this->station_id = $station_id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    public function getMechanicalBikes(): ?int
    {
        return $this->mechanical_bikes;
    }

    public function setMechanicalBikes(int $mechanical_bikes): static
    {
        $this->mechanical_bikes = $mechanical_bikes;

        return $this;
    }

    public function getElectricBikes(): ?int
    {
        return $this->electric_bikes;
    }

    public function setElectricBikes(int $electric_bikes): static
    {
        $this->electric_bikes = $electric_bikes;

        return $this;
    }
    public function getLastUpdatedAt(): ?\DateTimeInterface
    {
        return $this->lastUpdatedAt;
    }

    public function setLastUpdatedAt(\DateTimeInterface $lastUpdatedAt): self
    {
        $this->lastUpdatedAt = $lastUpdatedAt;
        return $this;
    }
}

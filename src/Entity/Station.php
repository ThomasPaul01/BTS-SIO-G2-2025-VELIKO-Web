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
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numBikesAvailable = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numBikesAvailableMechanical = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numBikesAvailableElectric = null;

    public function getStationId(): ?int
    {
        return $this->station_id;
    }

    public function setStationId(int $station_id): static
    {
        $this->station_id = $station_id;

        return $this;
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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

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

    public function getNumBikesAvailable(): ?int
    {
        return $this->numBikesAvailable;
    }

    public function setNumBikesAvailable(?int $numBikesAvailable): static
    {
        $this->numBikesAvailable = $numBikesAvailable;

        return $this;
    }

    public function getNumBikesAvailableMechanical(): ?int
    {
        return $this->numBikesAvailableMechanical;
    }

    public function setNumBikesAvailableMechanical(?int $numBikesAvailableMechanical): static
    {
        $this->numBikesAvailableMechanical = $numBikesAvailableMechanical;

        return $this;
    }

    public function getNumBikesAvailableElectric(): ?int
    {
        return $this->numBikesAvailableElectric;
    }

    public function setNumBikesAvailableElectric(?int $numBikesAvailableElectric): static
    {
        $this->numBikesAvailableElectric = $numBikesAvailableElectric;

        return $this;
    }
}

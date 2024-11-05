<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\IsEmailValide;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[IsEmailValide]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Le nom est requis.')]
    #[Assert\Length(min: 5, max: 20, minMessage: 'Le nom doit comporter au moins 5 caractères.', maxMessage: 'Le nom doit comporter au maximum 20 caractères.')]
    #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÿ\-]+$/', message: "Nom invalide.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Le prénom est requis.')]
    #[Assert\Length(min: 5, max: 20, minMessage: 'Le prénom doit comporter au moins 5 caractères.', maxMessage: 'Le prénom doit comporter au maximum 20 caractères.')]
    #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÿ\-]+$/', message: "Prénom invalide.")]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de naissance est requise.')]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: "L'adresse est requise.")]
    #[Assert\Length(min: 10, max: 100, minMessage: 'L\'adresse doit comporter au moins 10 caractères.', maxMessage: 'L\'adresse doit comporter au maximum 100 caractères.')]
    private ?string $address = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Le code postal est requis.')]
    #[Assert\Positive(message: 'Le code postal doit être un nombre positif.')]
    #[Assert\Regex(pattern: '/^\d{5}$/', message: 'Le code postal doit être composé de 5 chiffres.')]
    private ?string $postalCode = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'La ville est requise.')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'Le nom de la ville doit comporter au moins 2 caractères.', maxMessage: 'Le nom de la ville doit comporter au maximum 50 caractères.')]
    #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/', message: 'Le nom de la ville est invalide.')]
    private ?string $city = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }
}

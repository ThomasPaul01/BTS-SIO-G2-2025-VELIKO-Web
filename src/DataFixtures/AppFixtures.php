<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Station;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadAdmin($manager);

        $manager->flush();
    }
    private function loadAdmin(ObjectManager $manager): void
{
    // Admin
    $user = new User();
    $user->setEmail("admin@mail.dev");
    $user->setPassword($this->hasher->hashPassword($user, 'admin'));
    $user->setRoles(["ROLE_ADMIN"]);
    $user->setAddress("12 rue chezMoi");
    $user->setName("Admin");
    $user->setPostalCode("92200");
    $user->setCity("Paris");
    $user->setFirstName("Admin");
    $date=new \DateTime("2025-01-01");
    $user->setBirthdate($date);
    $user->setVerified(true);
    $user->setStatut(false);
    $user->setMustChangePassword(false);

    $manager->persist($user);
    $manager->flush();
}
    private function loadUsers(ObjectManager $manager): void
    {
               // Standard users
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user-$i@mail.dev");
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setRoles(["ROLE_USER"]);
            $user->setAddress("12 rue chezMoi");
            $user->setName("user");
            $user->setPostalCode("00000");
            $user->setCity("Paris");
            $user->setFirstName("user");
            $date=new \DateTime("2000-01-01");
            $user->setBirthdate($date);
            $user->setVerified(true);
            $user->setStatut(false);
            $user->setMustChangePassword(false);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

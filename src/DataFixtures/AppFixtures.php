<?php

namespace App\DataFixtures;

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

        $manager->flush();
    }
    private function loadUsers(ObjectManager $manager): void
    {
        // Admin
        $user = new User();
        $user->setEmail("admin@mail.dev");
        $user->setPassword($this->hasher->hashPassword($user, 'admin'));
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        // Standard users
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user-$i@mail.dev");
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);
        }

        $manager->flush();
    }

}

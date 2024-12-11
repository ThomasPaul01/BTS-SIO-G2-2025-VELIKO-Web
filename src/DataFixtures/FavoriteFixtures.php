<?php

namespace App\DataFixtures;

use App\Entity\Station;
use App\Entity\StationFav;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FavoriteFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadFavorites($manager);

        $manager->flush();
    }
    private function loadFavorites(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // verification du role de l'utilisateur
            if (in_array('ROLE_USER', $user->getRoles())) {

                //create 5 favorites for each user
                for ($i = 0; $i < 5; $i++) {

                    //get all stations
                    $stations = $manager->getRepository(Station::class)->findAll();
                    $stationIds = array_map(fn($station) => $station->getStationId(), $stations);

                    //create favorite
                    $favorite = new StationFav();
                    $favorite->setUserEmail($user->getEmail());
                    $favorite->setStationId($faker->randomElement($stationIds));

                    $manager->persist($favorite);
                }
            }
        }
    }
    public static function getGroups(): array
    {
        return ['Favorite'];
    }
}

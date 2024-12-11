<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Station;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReservationFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->loadReservation($manager);

        $manager->flush();
    }
    private function loadReservation(ObjectManager $manager): void
    {
        //create reservation for each user
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // verification du role de l'utilisateur
            if (in_array('ROLE_USER', $user->getRoles())) {

                //create 5 reservation for each user
                for ($i = 0; $i < 5; $i++) {
                    $reservation = new Reservation();
                    $reservation->setDateReservation($faker->dateTimeBetween('-1 month', '+1 month'));
                    $reservation->setUserEmail($user->getEmail());

                    $stations = $manager->getRepository(Station::class)->findAll();
                    $stationIds = array_map(fn($station) => $station->getStationId(), $stations);


                    $reservation->setIdStationFin($faker->randomElement($stationIds));
                    $reservation->setIdStationDepart($faker->randomElement($stationIds));
                    $reservation->setTypeVelo($faker->randomElement(['Evelo', 'Mecanique']));

                    $manager->persist($reservation);
                }
            }
        }
    }
    public static function getGroups(): array
    {
        return ['Reservation'];
    }
}

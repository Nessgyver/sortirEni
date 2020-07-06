<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etats;
use App\Entity\Participants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        //création des participants
        for ($i = 0; $i < 10; $i++) {
            $participant = new Participants();
            $participant->setUsername($faker->userName);
            $participant->setNom($faker->lastname);
            $participant->setPrenom($faker->firstNameMale);
            $participant->setTelephone($faker->e164PhoneNumber);
            $participant->setMail($faker->email);
            $participant->setPassword($faker->passWord);
            $participant->setRoles(["ROLE_USER"]);
            $participant->setAdministrateur(0);
            $participant->setActif(1);
            $participant->setCampusNoCampus($faker->numberBetween(1, 3));
            $manager->persist($participant);
        }

        //création des états pour les sorties
        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        foreach ($etats as $etat){
            $e = new Etats();
            $e->setLibelle($etat);
            $manager->persist($e);
        }

        //création des campus
        $campuses = ["Nantes", "Rennes", "Niort"];
        foreach ($campuses as $campus){
            $c = new Campus();
            $c->setNomCampus($campus);
            $manager->persist($c);
        }

        $manager->flush();
    }

}

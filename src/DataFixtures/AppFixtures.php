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
        $cree = new Etats();
        $cree->setLibelle("Créée");
        $manager->persist($cree);
        $ouverte = new Etats();
        $ouverte->setLibelle("Ouverte");
        $manager->persist($ouverte);
        $cloturee = new Etats();
        $cloturee->setLibelle("Cloturée");
        $manager->persist($cloturee);
        $enCours = new Etats();
        $enCours->setLibelle("Activité en cours");
        $manager->persist($enCours);
        $passee = new Etats();
        $passee->setLibelle("Passée");
        $manager->persist($passee);
        $annulee = new Etats();
        $annulee->setLibelle("Annulée");
        $manager->persist($annulee);

        //création des campus
        $nantes = new Campus();
        $nantes->setNomCampus("Nantes");
        $manager->persist($nantes);
        $rennes = new Campus();
        $rennes->setNomCampus("Rennes");
        $manager->persist($rennes);
        $niort = new Campus();
        $niort->setNomCampus("Niort");
        $manager->persist($niort);

        $manager->flush();
    }

}

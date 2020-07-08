<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encode;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encode = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $campusTest = new Campus();
        $villeTest = new Ville();

        //création des campus
        $campuses = ["Nantes", "Rennes", "Niort"];
        foreach ($campuses as $campus){
            $c = new Campus();
            $c->setNom($campus);
            $manager->persist($c);
            $campusTest = $c;
        }

        //création des participants

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setUsername($faker->userName);
            $participant->setNom($faker->lastname);
            $participant->setPrenom($faker->firstNameMale);
            $participant->setTelephone($faker->e164PhoneNumber);
            $participant->setMail($faker->email);
            $password = $this->encode->encodePassword($participant,"symfony");
            $participant->setPassword($password);
            $participant->setRoles(["ROLE_USER"]);
            $participant->setAdministrateur(0);
            $participant->setActif(1);
            $participant->setCampus($campusTest);

            $manager->persist($participant);
        }

        //création des états pour les sorties
        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        foreach ($etats as $etat){
            $e = new Etat();
            $e->setLibelle($etat);
            $manager->persist($e);
        }


        // création des villes
        for ($i = 0; $i < 5; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);
            $villeTest = $ville;
        }

        //creation des lieux
        for ($i = 0; $i < 5; $i++) {
            $lieu = new Lieu();
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);
            $lieu->setNom($faker->word);
            $lieu->setRue($faker->streetName);
            $lieu->setVille($villeTest);
            $manager->persist($lieu);
        }

        $manager->flush();
    }
}

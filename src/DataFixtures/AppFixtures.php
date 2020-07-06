<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etats;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //création des états pour les sorties
        $this->loadEtats();

        //création des campus
        $nantes = new Campus();
        $nantes->setNomCampus("Nantes");
        $rennes = new Campus();
        $rennes->setNomCampus("Rennes");
        $niort = new Campus();
        $niort->setNomCampus("Niort");

        $manager->flush();
    }

    public function loadEtats(): void
    {
        $cree = new Etats();
        $cree->setLibelle("Créée");
        $ouverte = new Etats();
        $ouverte->setLibelle("Ouverte");
        $cloturee = new Etats();
        $cloturee->setLibelle("Cloturée");
        $enCours = new Etats();
        $enCours->setLibelle("Activité en cours");
        $passee = new Etats();
        $passee->setLibelle("Passée");
        $annulee = new Etats();
        $annulee->setLibelle("Annulée");
    }
}

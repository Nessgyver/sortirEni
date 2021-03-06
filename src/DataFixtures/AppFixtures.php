<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\PhotoParticipant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Créé par Mathieu
 * Méthodes implémentées par Amandine, Damien et Mathieu
 *
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    private $encode;
    private $etatRepo;
    private $sortieRepo;
    private $participantEntities=[];
    private $lieuEntities=[];
    private $etatEntities=[];
    private $campusEntities=[];
    private $sortieEntities=[];

    public function __construct(UserPasswordEncoderInterface $encoder, EtatRepository $etatRepo, SortieRepository $sortieRepo){
        $this->encode = $encoder;
        $this->etatRepo = $etatRepo;
        $this->sortieRepo = $sortieRepo;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->setDefaultTimezone('Europe/Paris');
        date_default_timezone_set('UTC');

        //création des campus
        $campuses = ["Nantes", "Rennes", "Niort"];
        foreach ($campuses as $campus){
            $c = new Campus();
            $c->setNom($campus);
            $manager->persist($c);
            array_push($this->campusEntities, $c);
        }
        $manager->flush();

        //création des états pour les sorties
        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Annulée", "Passée"];
        foreach ($etats as $etat){
            $e = new Etat();
            $e->setLibelle($etat);
            $manager->persist($e);
            array_push($this->etatEntities, $e);
        }
        $manager->flush();

        //création des participants
        //création des participants au hasard grâce à faker
        for ($i = 0; $i < 20; $i++) {
            $participant = new Participant();
            $participant->setUsername($faker->userName);
            $participant->setNom($faker->lastname);
            $participant->setPrenom($faker->firstNameMale);
            $participant->setTelephone($faker->e164PhoneNumber);
            $participant->setMail($faker->email);
            $password = $this->encode->encodePassword($participant,"symfony");
            $participant->setPassword($password);

            //Le dernier participant en bdd sera un admin
            if ($i<19) {
                $participant->setAdministrateur(0);
                $participant->setRoles(["ROLE_USER"]);
            } else {
                $participant->setAdministrateur(1);
                $participant->setRoles(["ROLE_ADMIN"]);
            }
            $participant->setActif(1);
            $campus = $this->campusEntities[$faker->numberBetween(0,2)];
            $participant->setCampus($campus);
            array_push($this->participantEntities, $participant);

            //Créaton d'une photo pour le participant
            $photo = new PhotoParticipant();

            //les participants ont une photo par défaut qu'ils pourront modifier via leur profil
            $photo->setPhotoNom('default.png');
            $manager->persist($photo);
            $participant->setPhoto($photo);
            $manager->persist($participant);
        }
        $manager->flush();

        //creation des lieux avec leur ville
        for ($i = 0; $i < 5; $i++) {
            $lieu = new Lieu();
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);
            $lieu->setNom($faker->word);
            $lieu->setRue($faker->streetName);
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);
            $lieu->setVille($ville);
            array_push($this->lieuEntities, $lieu);
            $manager->persist($lieu);
        }
        $manager->flush();

        //création des sorties
        for($i = 0; $i < 200; $i++){
            $sortie = new Sortie();
            $lieu = $this->lieuEntities[$faker->numberBetween(0,3)];
            $sortie->setLieu($lieu);
            $sortie->setNom($faker->word);
            $sortie->setInfosSortie($faker->text(200));
            $sortie->setDuree($faker->numberBetween(10, 120));

            //la date de la sortie est comprise entre -10 jours et +20 jours par rapport à aujourd'hui
            $sortie->setDateHeureDebut($faker->dateTimeBetween('-10 days', '+20 days'));
            $dateDebut = $sortie->getDateHeureDebut();
            $dateDebutClone = clone $dateDebut;

            //la date limite d'inscription est la veille de la sortie
            $dateLimite = $dateDebutClone->modify('-1 day');
            $sortie->setDateLimiteInscription($dateLimite);

            $sortie->setNbInscriptionMax($faker->numberBetween(3, 10));
            $sortie->setUrlPhoto($faker->imageUrl(640, 480, null, true));
            $orga = $this->participantEntities[$faker->numberBetween(0,9)];
            $sortie->setOrganisateur($orga);

            //gestion de l'état en fonction de la date de début de la sortie
            if($dateDebut < (new DateTime())) {
                $sortie->setEtat($this->etatRepo->findOneBy([
                    'libelle'=>'Passée'
                ]));
            } else if($dateDebut==(new DateTime())){
                $sortie->setEtat($this->etatRepo->findOneBy([
                    'libelle'=>'Activité en cours'
                ]));
            } else {
                $libelle = $faker->randomElement(['Créée', 'Ouverte', 'Annulée']);
                $sortie->setEtat($this->etatRepo->findOneBy([
                    'libelle'=> $libelle
                ]));
            }
            array_push($this->sortieEntities, $sortie);
            $manager->persist($sortie);
        }
        echo (new DateTime())->format('Y-m-d H:i:s');
        $manager->flush();

        //création des inscriptions par sortie
        $sortiesFromBdd = $this->sortieRepo->findAll();

        foreach ($sortiesFromBdd as $sortie ){
            $etatSortie = $sortie->getEtat()->getLibelle();

            if($etatSortie!='Créée') {
                $nbInscriptionMax = $sortie->getNbInscriptionMax();
                $dateLimiteInscription = $sortie->getDateLimiteInscription();
                $dateLimiteInscriptionClone = clone $dateLimiteInscription;
                $dateDebutInscription = $dateLimiteInscriptionClone->modify('-15 day');
                $randomNbInscrits = $faker->numberBetween(0, $nbInscriptionMax);

                for($i = 0; $i < $randomNbInscrits; $i++){
                    $inscription = new Inscription();
                    $inscription->setSortie($sortie);
                    $indexMax = count($this->participantEntities)-1;
                    $offset = $faker->numberBetween(0, $indexMax);

                    if($indexMax!=0){
                        $participantInscrit = $this->participantEntities[$offset];
                        array_splice($this->participantEntities, $offset, 1);
                        $inscription->setParticipant($participantInscrit);
                        $inscription->setDateInscription($faker->dateTimeBetween($dateDebutInscription, $dateLimiteInscription));
                    } else {
                        break;
                    }

                    if($randomNbInscrits==$nbInscriptionMax){
                        $sortie->setEtat($this->etatRepo->findOneBy([
                            'libelle'=>'Clôturée'
                        ]));
                        $manager->persist($sortie);
                    }
                    $manager->persist($inscription);
                }
            }
        }
        $manager->flush();
    }

}

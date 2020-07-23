<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\RegistrationFormType;
use App\Form\UploadAdminType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Créé par Amandine
 * Méthodes implémentées par Amandine
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * TO DO: route permettant à un administrateur d'ajouter des villes en BDD
     * @Route("/villes", name="villes")
     */
    public function villes(VilleRepository $vr)
    {
        $listeVille = $vr->findAll();
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);

        return $this->render('admin/villes.html.twig', [
            'eltAGerer'     =>  'Villes',
            'listeVille'    =>  $listeVille,
            'villeForm'     =>  $villeForm->createView()
        ]);
    }

    /**
     * TO DO: route permettant à un administrateur d'ajouter des Campus en BDD
     * @Route("/campus", name="campus")
     */
    public function campus(CampusRepository $cr)
    {
        $listeCampus = $cr->findAll();
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);

        return $this->render('admin/campus.html.twig', [
            'eltAGerer'     =>  'Campus',
            'listeCampus'   =>  $listeCampus,
            'campusForm'    =>  $campusForm->createView()
        ]);
    }

    /**
     * TO DO: route permettant à un administrateur d'ajouter des Participants en BDD
     * @Route("/participants", name="participants")
     */
    public function choixAjoutParticipants()
    {
        return $this->render('admin/admin.html.twig');
    }

    /**
     * @Route("/participants/fichier", name="participants_fichier", methods={"GET", "POST"})
     */
    public function uploadParticipants(Request $request, EntityManagerInterface $em, CampusRepository $campusRepo, UserPasswordEncoderInterface $encoder)
    {
        //L'entité Admin sert à contenir les fichiers uploadés par l'admin
        $admin = new Admin();
        $formUpload = $this->createForm(UploadAdminType::class, $admin);
        $formUpload->handleRequest($request);
        try {
            if ($formUpload->isSubmitted() && $formUpload->isValid()) {

                $file = $admin->getFichierInscriptionFile();

                //traitement du fichier uploadé
                if($file) {
                    $safeFilename = uniqid();
                    $newFilename = $safeFilename . '.' . $file->getClientOriginalExtension();

                    //enregistrement du fichier dans le répertoire choisi
                    $file->move($this->getParameter('admin_dir'), $newFilename);
                    $admin->setFichierInscription($newFilename);
                    $em->persist($admin);

                    //récupération du contenu du fichier
                    $testJson = file_get_contents("uploads/admin/$newFilename");
                    $json = json_decode($testJson, true);

                    $listUsers=[]; //ce tableau servira à parcourir la liste des participants issus du fichier

                    try{
                        foreach ($json as $user) {

                            $participant = new Participant();
                            $participant
                                ->setNom($user['nom'])
                                ->setPrenom($user['prenom'])
                                ->setUsername($user['username'])
                                ->setTelephone($user['telephone'])
                                ->setMail($user['mail'])
                                ->setRoles($user['roles'])
                                ->setAdministrateur($user['administrateur'])
                                ->setActif($user['actif'])
                            ;

                            $campusId = $user['campus'];
                            $campus = $campusRepo->find($campusId);
                            $participant->setCampus($campus);

                            $password = $user['password'];
                            $unencodedPassword = $password;
                            $encodedPassword = $encoder->encodePassword($participant, $password);
                            $participant->setPassword($encodedPassword);

                            //photo par défault
                            $defaultPhoto = $participant->getDefaultPhoto();
                            $participant->setPhoto($defaultPhoto);

                            array_push($listUsers, $user);

                            $em->persist($defaultPhoto);

                            $em->persist($participant);
                        }

                        $em->flush();

                        return $this->render('admin/adminFileParticipant.html.twig',[
                            'formUpload' => $formUpload->createView(),
                            'listUsers' => $listUsers,
                            'campus' => $campus,
                            'unencodedPassword' => $unencodedPassword
                        ]);

                    } catch(Exception $e){ //La contrainte d'unicité du pseudo est vérifiée
                        $this->addFlash('error','Pseudo déjà utilisé: '.$user['username']);
                    }
                }

                $this->addFlash('success','Fichier envoyé avec succès');

            }

        } catch (Exception $e1) {
                $this->addFlash('error','Données non traitées, veuillez vous référer au modèle de saisie des données du fichier');

                return $this->render('admin/adminFileParticipant.html.twig',[
                    'formUpload' => $formUpload->createView()
                ]);
        }

        return $this->render('admin/adminFileParticipant.html.twig',[
            'formUpload' => $formUpload->createView()
        ]);
    }

    /**
     * @Route("/participants/formulaire", name="participants_form", methods={"GET", "POST"})
     */
    public function addParticipantForm(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){

        $participant = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //encodage du mot de passe en base de données
            $password = $participant->getPassword();
            $encodedPassword = $encoder->encodePassword($participant, $password);
            $participant->setPassword($encodedPassword);

            $role = $form["roles"]->getData();
            if ($role[0]==="ROLE_USER"){
                $participant->setAdministrateur(false);
            } else {
                $participant->setAdministrateur(true);
            }

            //Par défault le nouveau participant est actif
            $participant->setActif(true);

            //photo par défault
            $defaultPhoto = $participant->getDefaultPhoto();
            $participant->setPhoto($defaultPhoto);

            $em->persist($defaultPhoto);
            $em->persist($participant);
            
            $em->flush();

            $this->addFlash("success", "Participant ajouté en base de données");
            }



        return $this->render('admin/participants-form.html.twig', ['participant'=>$participant, 'form' => $form->createView()]);
    }

    /**
     * route permettant à un administrateur de désactiver un compte utilisateur en BDD
     * @Route("/desactiverUtilisateur/{id}", name="desactiverUtilisateur")
     */
    public function desactiverUtilisateur($id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        try {
            $participant = $participantRepository->findOneBy([
                'id' => $id,
            ]);

            $participant->setActif(false);
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été désactivé avec succès");
        } catch (Exception $e){
            $this->addFlash('error', "L'utilisateur n'a pas pu être désactivé");
        } finally {
            return $this->redirectToRoute('profil', [
                'id' => $id,
            ]);
        }

    }

    /**
     * route permettant à un administrateur de supprimer un compte utilisateur BDD
     * @Route("/supprimmerUtilisateur/{id}", name="supprimmerUtilisateur")
     */
    public function supprimmerUtilisateur($id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        try {
            $participant = $participantRepository->findOneBy([
                'id' => $id
            ]);

            $entityManager->remove($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte utilisateur a été correctement supprimé');

            return $this->redirectToRoute('home', [
            ]);
        }catch (Exception $e){
            $this->addFlash('error', "Erreur : Le compte utilisateur n'a pas été supprimé");

            return $this->redirectToRoute('profil', [
                'id' => $id,
            ]);
        }


    }

}

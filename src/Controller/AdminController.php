<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Participant;
use App\Entity\Campus;
use App\Entity\PhotoParticipant;
use App\Form\ParticipantAdminType;
use App\Form\ParticipantType;
use App\Form\RegistrationFormType;
use App\Form\UploadAdminType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
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
     * @Route("/villes", name="villes")
     */
    public function villes()
    {
        return $this->render('admin/villes.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/campus", name="campus")
     */
    public function campus()
    {
        return $this->render('admin/campus.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/participants", name="participants", methods={"GET", "POST"})
     */
    public function uploadParticipants(Request $request, EntityManagerInterface $em, CampusRepository $campusRepo, UserPasswordEncoderInterface $encoder)
    {
        //L'entité Admin sert à contenir les fichiers uploadés par l'admin
        $admin = new Admin();
        $formUpload = $this->createForm(UploadAdminType::class, $admin);
        $formUpload->handleRequest($request);

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
                        $defaultPhoto = new PhotoParticipant();
                        $defaultPhoto->setPhotoNom('default.png');
                        $finder = new Finder();
                        $defaultPhoto->setPhotoFile($finder->name($defaultPhoto->getPhotoNom())->in('uploads'));
                        $participant->setPhoto($defaultPhoto);

                        array_push($listUsers, $user);

                        $em->persist($defaultPhoto);
                        $em->persist($participant);
                    }

                    $em->flush();

                    return $this->render('admin/admin.html.twig',[
                        'formUpload' => $formUpload->createView(),
                        'listUsers' => $listUsers,
                        'campus' => $campus,
                        'unencodedPassword' => $unencodedPassword
                    ]);

                } catch(\Exception $e){ //La contrainte d'unicité du pseudo est vérifiée
                    $this->addFlash('error','Pseudo déjà utilisé: '.$user['username']);
                }
            }

            $this->addFlash('success','Fichier envoyé avec succès');

        }
        return $this->render('admin/admin.html.twig',[
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

            $em->persist($participant);
            $em->flush();

            $this->addFlash("success", "Participant ajouté en base de données");
            }



        return $this->render('admin/participants-form.html.twig', ['participant'=>$participant, 'form' => $form->createView()]);
    }

}

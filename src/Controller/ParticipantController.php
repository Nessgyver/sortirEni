<?php

namespace App\Controller;



use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\PhotoParticipantRepository;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class ParticipantController
 * Créé et implémenté par Amandine
 * Méthodes implémentées par Amandine
 * @package App\Controller
 */
class ParticipantController extends AbstractController
{
    /**
     * permet d'afficher le profil d'un participant
     * le rendu sera différent selon le statut de la personne connectée
     * et du profil auquel elle doit accéder
     * @Route("/profil/{id}", name="profil", methods={"GET", "POST"})
     */
    public function showProfile($id, ParticipantRepository $partiRepo, PhotoParticipantRepository $photoRepo, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator) {

        //Affichage du profil du user connecté
        $participantCo = $this->getUser();
        if($participantCo->getId()==$id) {
            //Recherche du participant connecté
            $participantCo = $partiRepo->find($id);
            $photoId = $participantCo ->getPhoto()->getId();
            $photo = $photoRepo->find($photoId);

            //Création du formulaire permettant la modification de profil
            $form = $this->createForm(ParticipantType::class, $participantCo);
            $form->handleRequest($request);

            try {
                if ($form->isSubmitted() && $form->isValid()) {

                    //encodage du mot de passe en base de données
                    $password = $participantCo->getPassword();
                    $encodedPassword = $encoder->encodePassword($participantCo, $password);
                    $participantCo->setPassword($encodedPassword);

                    //Traitement de la photo uploadée

                    $photoFile = $photo->getPhotoFile();
                    if ($photoFile) {
                        $safeFilename = uniqid();
                        $newFilename = $safeFilename .'.' . $photoFile->guessExtension();
                        $photo->setPhotoNom($newFilename);

                        //Déplace le fichier uploadé dans le répertoire public
                        try {
                            $photoFile->move(
                                $this->getParameter('upload_photo_dir'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        $participantCo->setPhoto($photo);
                    }
                    $em->persist($photo);
                    $em->persist($participantCo);
                    $em->flush();

                    $this->addFlash("success", "Profil mis à jour");

                    return $guardHandler->authenticateUserAndHandleSuccess(
                        $participantCo,
                        $request,
                        $authenticator,
                        'main' // firewall name in security.yaml
                    );
                }
            } catch(\Exception $e1){ //La contrainte d'unicité du pseudo est vérifiée
            $this->addFlash('error','Pseudo déjà utilisé');

            }

            return $this->render('participant/profil.html.twig', ['participantCo'=>$participantCo, 'form' => $form->createView(), 'id' => $id]);
        }

        //Si l'id de l'url ne correspond pas à l'utilisateur connecté, on cherche en bdd l'utilisateur correspondant à l'id
        else {
            $participantLambda = $partiRepo->find($id);

            return $this->render('participant/profil.html.twig', ['participantLambda'=>$participantLambda, 'id' => $id]);
        }


    }

}

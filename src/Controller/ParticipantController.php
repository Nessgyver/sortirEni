<?php

namespace App\Controller;



use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\PhotoType;
use App\Repository\ParticipantRepository;
use App\Repository\PhotoParticipantRepository;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class ParticipantController extends AbstractController
{
    /**
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

            if ($form->isSubmitted() && $form->isValid()) {

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

            return $this->render('participant/profil.html.twig', ['participantCo'=>$participantCo, 'form' => $form->createView(), 'id' => $id]);
        }

        //Si l'id de l'url ne correspond pas à l'utilisateur connecté, on cherche en bdd l'utilisateur correspondant à l'id
        else if($participantCo->getId()!=$id){
            $participantLambda = $partiRepo->find($id);

            return $this->render('participant/profil.html.twig', ['participantLambda'=>$participantLambda, 'id' => $id]);
        }


    }

}

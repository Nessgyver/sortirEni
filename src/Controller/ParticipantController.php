<?php

namespace App\Controller;



use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ParticipantController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil", methods={"GET", "POST"})
     */
    public function showProfile($id, ParticipantRepository $partiRepo, Request $request, EntityManagerInterface $em) {

        //Affichage du profil du user connecté
        $participantCo = $this->getUser();
        if($participantCo->getId()==$id) {
            //Recherche du participant connecté
            $participantCo = $partiRepo->find($id);

            //Création du formulaire permettant la modification de profil
            $form = $this->createForm(ParticipantType::class, $participantCo);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->merge($participantCo);
                $em->flush();

                $this->addFlash("success", "Profil mis à jour");
            }

            return $this->render('participant/profil.html.twig', ['participantCo'=>$participantCo, 'form' => $form->createView(), 'id' => $id]);
        }

        //
        else if($participantCo->getId()!=$id){
            $participantLambda = $partiRepo->find($id);

            return $this->render('participant/profil.html.twig', ['participantLambda'=>$participantLambda, 'id' => $id]);
        }


    }

}

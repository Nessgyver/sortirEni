<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function seDesister(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, Security $security)
    {
        $currentUser = $security->getUser();
        //Création de la route et récupération de l'id
        $route = 'sortie_afficher';
        $sortie = $sortieRepository->findOneBy([
            'id' => $id
        ]);

        //Récupération de l'inscription à la sortie sélectionnée par l'utilisateur courant
        $inscription = $inscriptionRepository->findBy([
            'sortie'        => $sortie,
            'participant'   => $currentUser
        ]);
        $entityManager->remove($inscription[0]);
        $entityManager->flush();

        $this->addFlash('success', 'vous avez été retiré de la liste des participants');
        return $this->redirectToRoute($route, [
                'id' => $sortie->getId()
            ]
        );
    }

    /**
     * @Route("/inscrire/{id}", name="inscrire")
     */
    public function inscrire(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        //Création de la route et récupération de l'id
        $route = 'sortie_afficher';
        $sortie = $sortieRepository->findOneBy([
            'id' => $id
        ]);

        //Si le nombre maximum d'inscriptions est atteint, l'état de la sortie passe sur close
        if ($sortie->getInscriptions()->count() == $sortie->getNbInscriptionMax()-1)
        {
            $sortie->setEtat($etatRepository->findOneBy([
                'id' => Sortie::CLOSED,
            ]));
            //ajoute un message pour prévenir que le nombre maximim est atteint
            $this->addFlash('error', 'Nombre maximum d\'inscrit atteint');
        }

        //Création et hydratation de l'inscription à insérer en base
        $inscription = new Inscription();
        $currentUser = $this->getUser();
        $inscription->setParticipant($currentUser);
        $inscription->setDateInscription(new DateTime());
        $inscription->setSortie($sortie);

        //Insertion en base
        $entityManager->persist($inscription);
        $entityManager->flush();

        //renvoie vers la page afficher_sortie
        return $this->redirectToRoute($route, [
                'id' => $sortie->getId()
            ]
        );
    }
}

<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EtatController extends AbstractController
{
    /**
     * @Route("/publier/{id}", name="sortie_publier")
     */
    public function publier(int $id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em)
    {
        //Création de la route et récupération de l'id
        $route = 'sortie_afficher';
        $sortie = $sortieRepository->findOneBy([
            'id' => $id
        ]);

        //Changement de l'état -> Ouverte
        $sortie->setEtat($etatRepository->findOneBy([
            'libelle' => 'Ouverte'
        ]));

        //Update en base
        try {
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'la modification a bien été prise en compte');
        }catch (Exception $e){
            $this->addFlash('error', 'une erreur est survenue, veuillez réessayer ultérieurement');
    }

        //renvoie vers la page sortie_afficher
        return $this->redirectToRoute('home');
    }

}

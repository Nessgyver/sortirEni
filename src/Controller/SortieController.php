<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/afficher", name="afficher")
     */
    public function afficher()
    {

        return $this->render('sortie/afficher.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/annuler", name="annuler")
     */
    public function annuler()
    {
        return $this->render('sortie/annuler.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     */
    public function creer(Request $request, EntityManagerInterface $em, EtatRepository $etatRepository)
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        //si le formulaire de création de sortie est soumis et toutes les données sont valides,
        //la sortie est ajoutée en base de données
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisateur($this->getUser());
            //récupère la valeur du bouton cliqué pour modifier le champ état de la sortie
            if($sortieForm->get('enregistrer')->isClicked())
            {
                $sortie->setEtat($etatRepository->findOneBy([
                    'libelle'=>'Créée'
                ]));
            }
            elseif ($sortieForm->get('publier')->isClicked())
            {
                $sortie->setEtat($etatRepository->findOneBy([
                    'libelle'=>'Ouverte'
                ]));
            }
            elseif ($sortieForm->get('supprimer')->isClicked())
            {
                return $this->redirectToRoute('sortie_annuler');
            }
            elseif ($sortieForm->get('annuler')->isClicked())
            {
                return $this->redirectToRoute('home');
            }



            $em->persist($sortie);
            $em->flush();

        }


        return $this->render('sortie/creer.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        //si le formulaire de création de sortie est soumis et toutes les données sont valides,
        //la sortie est ajoutée en base de données
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisteur($this->getUser());
            //récupère la valeur du bouton cliqué pour modifier le champ état de la sortie
            if($sortieForm->get('enregistrer')->isClicked())
            {
                $sortie->setEtat($etatRepository->findOneBy([
                    'libelle'=>'Créée'
                ]));
            }
            elseif ($sortieForm->get('publier')->isClicked())
            {
                $sortie->setEtat($etatRepository->findOneBy([
                    'libelle'=>'Ouverte'
                ]));
            }
            elseif ($sortieForm->get('supprimer')->isClicked())
            {
                return $this->redirectToRoute('sortie_annuler');
            }
            elseif ($sortieForm->get('annuler')->isClicked())
            {
                return $this->redirectToRoute('home');
            }

            $em->persist($sortie);
            $em->flush();

        }


        return $this->render('sortie/modifier.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

}

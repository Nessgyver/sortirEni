<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\SortieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(SortieRepository $sortieRepository, Request $request)
    {
        //création du formulaire de filtration des sorties affichées
        $listeSortiesForm = $this->createForm(ListeSortieType::class, null, ['required'=>false]);

        //initialisation des valeurs pour l'affichage
        $dateDuJour = (new DateTime())->format("d/m/Y");

        //récupération du formulaire de filtration des sorties si soummis
        $data = $listeSortiesForm->handleRequest($request)->getData();

        //traitement du formulaire de filtration des sorties
        if ($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            $listeSorties = $sortieRepository->findByFilters($data);
        } else {
            $listeSorties = $sortieRepository->findByFilters($data);
        }

        //génération des variables à transmettre à la page à afficher
        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'dateDuJour' => $dateDuJour,
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

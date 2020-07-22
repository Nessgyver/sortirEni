<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\SortieRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * créée et implémentée par Mathieu
 * ce contrôleur gère la page d'accueil
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(SortieRepository $sortieRepository, Request $request, PaginatorInterface $paginator)
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
            $donnees = $sortieRepository->findByFilters($data);
            $listeSorties = $paginator -> paginate(
                $donnees,
                $request->query->getInt('page', 1),
                20
            );
        } else {
            $donnees = $sortieRepository->findByFilters($data);
            $listeSorties = $paginator -> paginate(
                $donnees,
                $request->query->getInt('page', 1),
                20
            );
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

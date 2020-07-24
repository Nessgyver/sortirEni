<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\SortieRepository;
use DateInterval;
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

        //récupération du formulaire de filtration des sorties si soummis
        $data = $listeSortiesForm->handleRequest($request)->getData();

        //traitement du formulaire de filtration des sorties
        if ($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            $donneesRaw = $sortieRepository->findByFilters($data);
            $donneesSansArchivees = $this->getListeSortiesSansArchivees($donneesRaw);
            $listeSorties = $paginator -> paginate(
                $donneesSansArchivees,
                $request->query->getInt('page', 1),
                100
            );
        } else {
            $donneesRaw = $sortieRepository->findByFilters($data);
            $donneesSansArchivees = $this->getListeSortiesSansArchivees($donneesRaw);
            $listeSorties = $paginator -> paginate(
                $donneesSansArchivees,
                $request->query->getInt('page', 1),
                100
            );
        }

        //génération des variables à transmettre à la page à afficher
        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }

    /**
     * Fonction retournant une liste de Sortie sans les sorties archivées depuis plus de 30 jours
     *
     * @param $listeSortie
     * @return mixed
     */
    public function getListeSortiesSansArchivees($listeSortie)
    {
        //Création d'une variable DateTime pour maintenant
        $dateNow = new DateTime();

        //Boucle sur la liste des sorties
        foreach ($listeSortie as $s => $sortie)
        {
            //Récupération de la date de chaque sortie et ajout de 30 jours pour vérifier l'archivage
            $dateArchivage = $sortie->getDateHeureDebut();
            $dateArchivage->add(new DateInterval('P30D'));

            //Si la dateArchivage est inférieure à la dateNow, alors l'archivage a duré plus de 30 jours et elle ne sera pas gardée
            //Sinon la sortie est conservée
            if ( $dateArchivage < $dateNow)
            {
                unset($listeSortie[$s]);
            }
            $dateArchivage->sub(new DateInterval('P30D'));
        }
        return $listeSortie;
    }
}

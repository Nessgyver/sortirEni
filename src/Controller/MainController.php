<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
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
        $listeSorties = $sortieRepository->findAll();


        $listeSortiesForm = $this->createForm(ListeSortieType::class);
        $data = $listeSortiesForm->handleRequest($request)->getData();
        $motCle = $data['nomSortie'];
        $campus = $data['campus'];
        $dataFiltes = $data['Filtres'];
        $dataMotCle = $data['nomSortie'];
        $dataDateDebut = $data['dateDebut'];
        $dataDateFin = $data['dateFin'];




        if($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {

            //Liste des sorties si je suis organisateur
            if (in_array(0, $dataFiltes))
            {

                $listeSorties =  array_merge($listeSorties, $sortieRepository->findByOrganisateur());
            }

            //Liste des sorties si je suis inscrit
            if (in_array(1, $dataFiltes))
            {
                $listeSorties =  array_merge($listeSorties, $sortieRepository->findSubscribed());

            }

            //Liste des sorties si je NE suis PAS inscrit
            if (in_array(2, $dataFiltes))
            {
                $listeSorties =  array_merge($listeSorties, $sortieRepository->findUnsubscribed());

            }

            //Liste des sorties archivées(+ d'un mois)
            if (in_array(3, $dataFiltes))
            {
                $listeSorties =  array_merge($listeSorties, $sortieRepository->findByArchivedSortie());

            }


            //Filtre mot clé
            if ($motCle)
            {
                foreach ($listeSorties as $sortie)
                {
                    if(!str_contains($sortie->getNom(), $motCle))
                    {
                        $sortie->unset();
                    }
                }

            }




            //$listeSorties = $sortieRepository->findByCampus('Nantes');
            //$listeSorties = $sortieRepository->findByCampus($campus);
        }




        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

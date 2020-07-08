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
    public function home(SortieRepository $sortieRepository, Request $request, InscriptionRepository $inscriptionRepository)
    {
        $listeSorties = $sortieRepository->findAll();

        $listeSortiesForm = $this->createForm(ListeSortieType::class);
        $data = $listeSortiesForm->handleRequest($request)->getData();

        if($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            if (in_array(0, $data))
            {
                $sortiesOrganisateur = $sortieRepository->findByOrganisateur();

            }
            if (in_array(1, $data))
            {
                $participantsInscrits = $inscriptionRepository->findBySubscribedSorties();

            }
            if (in_array(2, $data))
            {
                $participantsNonInscrits = $inscriptionRepository->findByUnsubscribedSorties();

            }
            if (in_array(3, $data))
            {
                $sortiesArchivees = $sortieRepository->findByArchivedSortie();

            }
        }

        var_dump($data);



        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

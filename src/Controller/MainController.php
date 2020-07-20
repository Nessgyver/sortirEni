<?php

namespace App\Controller;

use App\Form\ListeSortieType;
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


        $listeSortiesForm = $this->createForm(ListeSortieType::class);
        $data = $listeSortiesForm->handleRequest($request)->getData();
        $listeSorties = $sortieRepository->findByFilters($data);

        if ($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            $listeSorties = $sortieRepository->findByFilters($data);
            //dd($data);
        }




        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

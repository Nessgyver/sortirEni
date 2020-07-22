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


        $listeSortiesForm = $this->createForm(ListeSortieType::class, null, ['required'=>false]);
        $data = $listeSortiesForm->handleRequest($request)->getData();

        if ($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            $listeSorties = $sortieRepository->findByFilters($data);
        } else {
            $listeSorties = $sortieRepository->findByFilters($data);
        }




        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

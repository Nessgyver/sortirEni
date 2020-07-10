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
        $listeSorties = $sortieRepository->findAll();


        $listeSortiesForm = $this->createForm(ListeSortieType::class);
        $data = $listeSortiesForm->handleRequest($request)->getData();

        if ($listeSortiesForm->isSubmitted() && $listeSortiesForm->isValid())
        {
            unset($listeSortiesForm);
            $listeSorties = $sortieRepository->findByFilters($data);
            $listeSortiesForm = $this->createForm(ListeSortieType::class);
        }



        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$listeSortiesForm->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(SortieRepository $sortieRepository)
    {
        $listeSorties = $sortieRepository->findAll();

        $form = $this->createForm(ListeSortieType::class);

        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$form->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

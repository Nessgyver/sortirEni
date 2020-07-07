<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ListeSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(EntityManagerInterface $em)
    {
        $sortieRepo = $em->getRepository(Sortie::class);
        $listeSorties = $sortieRepo->findAll();




        $form = $this->createForm(ListeSortieType::class);

        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
            'form'=>$form->createView(),
            'listeSorties' => $listeSorties,
        ]);
    }
}

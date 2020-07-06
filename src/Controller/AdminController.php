<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/villes", name="villes")
     */
    public function villes()
    {
        return $this->render('admin/villes.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/campus", name="campus")
     */
    public function campus()
    {
        return $this->render('admin/campus.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}

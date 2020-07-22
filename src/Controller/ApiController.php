<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * api permettant de récupérer toutes les infos concernant un lieu
     * prend en paramètre l'id du lieu concerné
     * @Route("/api/1/lieu")
     */
    public function getLieu(Request $request, LieuRepository $lr){
        //on récupère le lieu sélectionné
        $lieuId = $request->query->get('lieuId');
        $lieu = $lr->findById($lieuId);

        //on renvoie toutes les infos utiles sous forme de response JSON
        return $this->json([
           'lieu'  =>  $lieu,
           'status'         =>  "ok"
        ], 200);
    }
}
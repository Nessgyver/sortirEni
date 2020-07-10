<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/1/lieux")
     */
    public function getLieux(Request $request, LieuRepository $lr, VilleRepository $vr){
        //on récupère la ville sélectionnée
        $villeId = $request->query->get('villeId');
        $ville = $vr->find($villeId);

        //on charge tous les lieux associés à la ville
        $lieuxAssocies = $lr->findByVilleId($ville);

        //on renvoie toutes les infos utiles sous forme de response JSON
        return $this->json([
           'lieuxAssocies'  =>  $lieuxAssocies,
           'status'         =>  "ok"
        ], 200);
    }
}
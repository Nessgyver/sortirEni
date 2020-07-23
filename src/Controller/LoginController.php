<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class LoginController
 * Créée et implémentée par Amandine
 * Méthodes implémentées par Amandine
 * @package App\Controller
 */
class LoginController extends AbstractController
{
    /**
     * renvoie vers la page qui permet à l'utilisateur de se connecter à la plateforme
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('actif');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/Actif", name="actif")
     */
    public function isAccessGranted(ParticipantRepository $pr)
    {
        $participant = $pr->findOneBy([
            'username'  =>  $this->getUser()->getUsername()
        ]);
        if ($participant->isActif()){
            return $this->redirectToRoute('home');
        }else{
            $this->addFlash('warning', 'Votre compte a été désactivé, veuillez contacter un administrateur');
            return $this->redirectToRoute('logout');
        }
    }

    /**
     * la route est interprétée par symfony directement et renvoie vers la page de connexion
     * dans notre cas, on ne rentre pas dans la méthode ou elle renvoie une erreur
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

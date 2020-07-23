<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * contrôleur créé par Mathieu pour les routes
 * méthodes seDesister, inscrire et publier implémentées par Mathieu
 * autres méthodes implémentées par Damien
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/afficher/{id}", name="afficher")
     */
    public function afficher($id, EntityManagerInterface $em)
    {
        //récupère la sortie passée en $id pour l'afficher
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $sortieForm = $this->createForm(SortieType::class, $sortie,['disabled'=>true]);

        //récupère la liste des participants associés à cette sortie
        $inscriptions = $sortie->getInscriptions();

        //injecte les infos pour pouvoir les afficher
        return $this->render('sortie/afficher.html.twig', [
            'sortieForm'    => $sortieForm->createView(),
            'inscriptions'  => $inscriptions,
            'sortie'        => $sortie
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function annuler($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        //récupère la sortie passée en $id pour l'afficher
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        //vérifie que l'utilisateur est bien autorisé à accéder à la page demandée
        if($this->getUser()->getUsername() !== $sortie->getOrganisateur()->getUsername() && !$this->isGranted(['ROLE_ADMIN']))
        {
            return $this->redirectToRoute('sortie_afficher', [
                'id'=> $id
                ])
            ;
        }

        //créé un nouveau formulaire spécifique à cette page
        $annulationForm = $this->createFormBuilder()
            ->add('motif', TextareaType::class)
            ->add('enregistrer', SubmitType::class, [
                'label'=> 'Enregistrer'
            ])
            ->getForm()
        ;

        //récupère les infos du formulaire s'il a été soumis
        $annulationForm->handleRequest($request);
        //traite le formulaire le cas échéant
        if($annulationForm->isSubmitted() && $annulationForm->isValid())
        {
           $data = $annulationForm->getData();
           $motif = $data['motif'];
           $sortie->setMotifAnnulation($motif);
           $etatAnnule = $etatRepository->findOneBy([
               'libelle'=>'Annulée'
           ]);
           $sortie->setEtat($etatAnnule);
           $em->persist($sortie);
           $em->flush();
            //ajoute un message pour informer l'utilisateur que son action a bien été prise en compte
           $this->addFlash('success', 'la sortie a bien été annulée');
            //renvoie vers la page d'accueil
           return $this->redirectToRoute('home');
        }
        //affiche la page concernée
        return $this->render('sortie/annuler.html.twig',[
            'sortie'=>$sortie, 'annulationForm'=>$annulationForm->createView()
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     */
    public function creer(Request $request, EntityManagerInterface $em, EtatRepository $etatRepository)
    {
        //créé une nouvelle sortie pour pouvoir créer un formulaire vide
        $sortie = new Sortie();
        //assignation automatique de dates pour corriger bug ajax
        $now = DateTime::createFromFormat('d/m/Y H:i',
            (new DateTime())
                ->add(date_interval_create_from_date_string('2 days'))
                ->format('d/m/Y H:i'));
        $sortie->setDateHeureDebut($now)->setDateLimiteInscription($now);
        //créé un formulaire en passant les options nécessaires
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'optionBoutons'=>'creer',
        ]);

        //récupère les informations issues du formulaire
        $sortieForm->handleRequest($request);
        //si le formulaire de création de sortie est soumis et toutes les données sont valides,
        //la sortie est ajoutée en base de données
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisateur($this->getUser());
            //fait appel à la méthode qui gère la redirection après soumission d'un formulaire
            return $this->redirectionFormulaire($sortieForm, $sortie, $etatRepository, $em);
        }
        //renvoie vers la page de création de sortie
        return $this->render('sortie/creer.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        //récupère la sortie passée en id pour pouvoir la modifier
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        //créé le formulaire correspondant
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'optionBoutons'=>'modifier',
        ]);

        //vérifie que l'utilisateur est bien autorisé à accéder à la page demandée
        if($this->getUser()->getUsername() !== $sortie->getOrganisateur()->getUsername() && !$this->isGranted(['ROLE_ADMIN']))
        {
            return $this->redirectToRoute('sortie_afficher', [
                'id'=> $id
            ])
                ;
        }

        //récupère les infos issues de la soumission du formulaire
        $sortieForm->handleRequest($request);
        //si le formulaire de création de sortie est soumis et toutes les données sont valides,
        //la sortie est ajoutée en base de données
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisateur($this->getUser());
            //fait appel à la méthode qui gère la redirection après soumission d'un formulaire
            return $this->redirectionFormulaire($sortieForm, $sortie, $etatRepository, $em);
        }
        //renvoie vers la page de modification
        return $this->render('sortie/modifier.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * récupère la valeur du bouton cliqué pour modifier le champ état de la sortie si besoin
     * enregistre en base de données le cas échéant
     * et oriente sur la page appropriée
     * @param FormInterface $sortieForm
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    private function redirectionFormulaire(FormInterface $sortieForm, Sortie $sortie, EtatRepository $etatRepository, EntityManagerInterface $em): RedirectResponse
    {
        $route = 'sortie_';
        $persist = false;
        //récupère la valeur du bouton cliqué pour modifier le champ état de la sortie
        if ($sortieForm->get('enregistrer')->isClicked()) {
            $sortie->setEtat($etatRepository->findOneBy([
                'libelle' => 'Créée'
            ]));
            $route .= 'afficher';
            $persist = true;
        } elseif ($sortieForm->get('publier')->isClicked()) {
            $sortie->setEtat($etatRepository->findOneBy([
                'libelle' => 'Ouverte'
            ]));
            $route .= 'afficher';
            $persist = true;
        } elseif ($sortieForm->get('supprimer')->isClicked()) {
            $route .= 'annuler';
        } elseif ($sortieForm->get('annuler')->isClicked()) {
            $route = 'home';
        }
        if($persist)
        {
            $em->persist($sortie);
            $em->flush();
        }
        //renvoie vers la route adéquate
        return $this->redirectToRoute($route, [
                'id' => $sortie->getId()
            ]
        );
    }
}

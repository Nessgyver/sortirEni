<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * contrôleur créé par Mathieu pour les routes
 * méthodes implémentées par Damien
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

        //to do: récupère la liste des participants associés à cette sortie

        return $this->render('sortie/afficher.html.twig', [
            'sortieForm'=> $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function annuler($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        $sortie = new Sortie();
        //récupère la sortie passée en $id pour l'afficher
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $annulationForm = $this->createFormBuilder()
            ->add('motif', TextareaType::class)
            ->add('enregistrer', SubmitType::class, [
                'label'=> 'Enregistrer'
            ])
            ->add('annuler', SubmitType::class, [
                'label'=> 'Retour'
            ])
            ->getForm()
        ;

        $annulationForm->handleRequest($request);

        if($annulationForm->isSubmitted() && $annulationForm->isValid())
        {
           $data = $annulationForm->getData();
           $motif = $data['motif'];
           $sortie->setMotifAnnulation($motif);
           $etatAnnule = $etatRepository->findOneBy([
               'libelle'=>'Annulée']
           );
           $sortie->setEtat($etatAnnule);
           $em->persist($sortie);
           $em->flush();

           $this->addFlash('success', 'la sortie a bien été annulée');

           return $this->redirectToRoute('home');
        }

        return $this->render('sortie/annuler.html.twig',[
            'sortie'=>$sortie, 'annulationForm'=>$annulationForm->createView()
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     */
    public function creer(Request $request, EntityManagerInterface $em)
    {
        //créé une nouvelle sortie pour pouvoir créer un formulaire vide
        $sortie = new Sortie();
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

            return $this->redirectionFormulaire($sortieForm, $sortie, $etatRepository, $em);

        }


        return $this->render('sortie/creer.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        //récupère la sortie passée en id pour
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'optionBoutons'=>'modifier',
        ]);

        $sortieForm->handleRequest($request);


        //si le formulaire de création de sortie est soumis et toutes les données sont valides,
        //la sortie est ajoutée en base de données
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisateur($this->getUser());

            return $this->redirectionFormulaire($sortieForm, $sortie, $etatRepository, $em);

        }

        return $this->render('sortie/modifier.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /**
     * récupère la valeur du bouton cliqué pour modifier le champ état de la sortie si besoin
     * enregistre en base de données le cas échéant
     * et oriente sur la page appropriée
     * @param \Symfony\Component\Form\FormInterface $sortieForm
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirectionFormulaire(\Symfony\Component\Form\FormInterface $sortieForm, Sortie $sortie, EtatRepository $etatRepository, EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
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

        return $this->redirectToRoute($route, [
                'id' => $sortie->getId()
            ]
        );
    }

}

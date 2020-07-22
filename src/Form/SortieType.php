<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * classe de formulaire permettant de créer, de modifier ou d'afficher des sorties
 * implémentée par damien
 * Class SortieType
 * @package App\Form
 */
class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionBoutons = $options['optionBoutons'];
        //création des champs du formulaire de base
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class,[
                'widget'        => 'single_text',
                'with_seconds'  => false,
                'attr'          =>  [
                    'min'   => '21/07/2020'
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class,[
                'widget'    => 'single_text',
            ])
            ->add('nbInscriptionMax')
            ->add('duree')
            ->add('infosSortie')
            ->add('organisateur', EntityType::class,[
                'class'         => Participant::class,
                'choice_label'  => function(Participant $p){
                    return $p->getCampus()->getNom();
                },
                'disabled'      =>true,
                'label'         =>'Campus'
            ])
            ->add('ville', EntityType::class,[
                'class'         => Ville::class,
                'choice_label'  => function(Ville $v){
                    return $v->getNom();
                },
                'mapped'        => false,
                'placeholder'   => 'veuillez sélectionner une ville'
            ]);

            //ajout du listener pour générer dynamiquement le champ pour sélectionner le lieu en fonction de la ville choisie
            $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
                    function(FormEvent $event)
                    {
                        $form = $event->getForm();
                        $this->addLieuField($form->getParent(), $form->getData());
                    }
            );

            //ajout du listener permettant d'afficher les informations de ville et de lieu
            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event){
                    $data = $event->getData();
                    $lieu = $data->getLieu();
                    $form = $event->getForm();
                    if($lieu){
                        $ville = $lieu->getVille();
                        $this->addLieuField($form, $ville);
                        $form->get('ville')->setData($ville);
                    }else{
                        $this->addLieuField($form, null);
                    }
                }
            );


            if($optionBoutons == 'modifier' || $optionBoutons == 'creer')
            {
                $builder
                ->add('enregistrer', SubmitType::class, [
                'label'=> 'Enregistrer'
                ]);
                if($optionBoutons == 'modifier' || $optionBoutons == 'creer')
                {
                    $builder
                    ->add('publier', SubmitType::class, [
                        'label'=> 'Publier'
                    ]);
                    if($optionBoutons == 'modifier')
                    {
                        $builder
                        ->add('supprimer', SubmitType::class, [
                            'label'=> 'Supprimer'
                        ]);
                    }

                };
            }
    }

    /**
     * permet d'ajouter le champ de Lieu et de modifier son contenu dynamiquement en fonction de la ville sélectionnée
     * @param FormInterface $form
     * @param Ville|null $ville
     */
    private function addLieuField(FormInterface $form, ?Ville $ville){
        $form
            ->add('lieu', EntityType::class,[
                'class'         => Lieu::class,
                'choices'       => $ville != null ? $ville->getLieu() :[],
                'choice_label'  => function(Lieu $l){
                    return $l->getNom();
                },
                'placeholder'   => 'veuillez sélectionner un lieu'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'optionBoutons' => 'aucun',
        ]);
    }
}

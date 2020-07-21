<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class,[
                'widget'    => 'single_text',
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
            $builder->get('ville')->addEventListener(
        FormEvents::POST_SET_DATA,
                function(FormEvent $event)
                {
                    $form = $event->getForm();
                    $form->getParent()
                        ->add('lieu', EntityType::class,[
                            'class'         => Lieu::class,
                            'choices'       => $form->getData() != null ? $form->getData()->getLieu() :[],
                            'choice_label'  => function(Lieu $l){
                                return $l->getNom();
                            },
                            'placeholder'   => 'veuillez sélectionner un lieu'
                        ]);
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'optionBoutons' => 'aucun',
        ]);
    }
}

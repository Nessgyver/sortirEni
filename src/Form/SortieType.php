<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', )
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('duree')
            ->add('infosSortie')
            ->add('organisateur', EntityType::class,[
                'class' => Participant::class,
                'choice_label'=> function(Participant $p){
                    return $p->getCampus()->getNom();
                },
                'disabled' =>true
            ])
//            ->add('ville', EntityType::class,[
//                'class' => Ville::class,
//                'choice_label' => function(Ville $v){
//                    return $v->getNom();
//                }
//            ])
            ->add('lieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => function(Lieu $l){
                    return $l->getNom();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

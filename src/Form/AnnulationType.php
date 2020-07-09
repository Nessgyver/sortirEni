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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'disabled'
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'disabled'
            ])
            ->add('lieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => function(Lieu $l){
                    return $l->getNom();
                },
                'disabled'
            ])
            ->add('motif', TextareaType::class)
            ->add('enregistrer', SubmitType::class, [
                'label'=> 'Enregistrer'
            ])
            ->add('annuler', SubmitType::class, [
                'label'=> 'Annuler'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
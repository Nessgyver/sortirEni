<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;

class ListeSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label'=> 'nom',
                'placeholder' => 'Choisissez un campus'
            ])
            ->add('nomSortie', TextType::class, [
                'label'=>'Le nom de la sortie contient : ',
                'attr' => [
                  'placeholder' => 'Affines ta recherche !'
                ],
                'constraints' => [
                   new Length([
                       'min' => 3,
                       'minMessage' => 'Veuillez écrire minimum 3 caractères'
                   ]),
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre ',
                'format' => 'dd/MM/yyyy',

            ])
            ->add('dateFin', DateType::class, [
                'label' => ' et ',
                'format' => 'dd/MM/yyyy',
            ])
            ->add('Filtres', ChoiceType::class, [
                'label' => false,
                'choices' =>  [
                    'Sorties dont je suis l\'organisateur/rice' => 0,
                    'Sortie auxquelles je suis inscrit/e' => 1,
                    'Sorties auxquelles je ne suis pas inscrit/e'=> 2,
                    'Sorties passées' => 3,
                ],
                'expanded' => true,
                'multiple' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

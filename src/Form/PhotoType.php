<?php

namespace App\Form;

use App\Entity\PhotoParticipant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Créé par Amandine
 * Méthodes implémentées par Amandine
 * Class PhotoType
 * @package App\Form
 */
class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photoFile', FileType::class, [
                'label' => 'Ma photo: ',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoParticipant::class,
        ]);
    }
}

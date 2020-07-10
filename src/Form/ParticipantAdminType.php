<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantAdminType extends ParticipantType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('roles',ChoiceType::class,
            array('choices' => array(
                'participant' => '["ROLE_USER"]',
                'administrateur' => '["ROLE_ADMIN"]',
                'choices_as_values' => true,'multiple'=>false,'expanded'=>true)
                ))
                ->add('actif',ChoiceType::class,
                    array('choices' => array(
                        'actif' => '1',
                        'inactif' => '0',
                        'choices_as_values' => true,'multiple'=>false,'expanded'=>true)
                    ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

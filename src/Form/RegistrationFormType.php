<?php

namespace App\Form;

use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo: '
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom: '
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom: '
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone: '
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email: '
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe: '],
                'second_options' => ['label' => 'Confirmation: '],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add("campus", null, [
                'label' => "Campus: ",
                'choice_label' => "nom",
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('c')->addOrderBy('c.nom', 'ASC');
                }
            ])
            ->add('roles',ChoiceType::class,
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
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

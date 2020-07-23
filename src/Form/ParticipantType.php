<?php

namespace App\Form;

use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Créé par Amandine
 * Méthodes implémentées par Amandine
 *
 * Class ParticipantType
 * @package App\Form
 */
class ParticipantType extends AbstractType
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
            ])
            ->add("campus", null, [
                'label' => "Campus: ",
                'choice_label' => "nom",
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('c')->addOrderBy('c.nom', 'ASC');
                }
            ])
            ->add('photo', PhotoType::class, [
                'required' => false
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

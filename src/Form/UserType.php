<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login',
                TextType::class,
                ['label' => 'Identifiant '])
            ->add('password',
                PasswordType::class,
                ['label' => 'Mot de passe '])
            ->add('name',
                TextType::class,
                ['label' => 'Nom '])
            ->add('firstname',
                TextType::class,
                ['label' => 'Prénom '])
            ->add('birthdate',
                DateType::class,
                ['label' => 'Date de naissance ',
                    'years' => range(date('Y')-100, date('Y'))])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

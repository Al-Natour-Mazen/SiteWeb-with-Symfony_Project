<?php

/********************************************/
/*          PROJET TECHNOLOGIE WEB 2        */
/*     AL NATOUR MAZEN && CAILLAUD TOM      */
/********************************************/

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
                TextType::class,
                ['label' => 'Nom '])
            ->add('prenom',
                TextType::class,
                ['label' => 'Prénom '])
            ->add('login',
            TextType::class,
                ['label' => 'Identifiant '])
            ->add('password',
                PasswordType::class,
                 ['label' => 'Mot de passe '])
            ->add('dateNaissance',
                DateType::class,
                ['widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'Date de naissance'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

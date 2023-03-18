<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle',
                TextType::class,
                ['label' => 'Libelle  '])
            ->add('prixUnitaire',
                IntegerType::class,
                ['label' => 'PrixUnitaire '])
            ->add('quantite',
                IntegerType::class,
                ['label' => 'Quantite '])
            ->add('description',
                TextareaType::class,
                ['label' => 'Description ',
                    'attr' => ['placeholder' => 'facultatif',
                               'style' => 'height : 100px; width: 400px ; resize: none;',
                               'maxlength' => 500],
                    'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

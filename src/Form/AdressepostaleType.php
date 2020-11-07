<?php

namespace App\Form;

use App\Entity\AdressePostale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdressepostaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroRue',TextType::class)
            ->add('rue',TextType::class)
            ->add('ville',TextType::class)
            ->add('departement',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdressePostale::class,
        ]);
    }
}


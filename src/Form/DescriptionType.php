<?php

namespace App\Form;

use App\Entity\Description;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('miniBio',TextType::class)
            ->add('fumeur',ChoiceType::class, [
            'choices'=>[
                'Oui' => true,
                'non' => false,
                ],
            ])
            ->add('bavard',ChoiceType::class, [
            'choices'=>[
                'Oui' => true,
                'non' => false,
                ],
            ])
            ->add('animaux',ChoiceType::class, [
            'choices'=>[
                'Oui' => true,
                'non' => false,
                ],
            ])
            ->add('centreInterets',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}

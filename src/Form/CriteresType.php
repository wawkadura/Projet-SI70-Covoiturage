<?php

namespace App\Form;

use App\Entity\Criteres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CriteresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fumeur',ChoiceType::class, [
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
            ->add('valise',ChoiceType::class, [
            'choices'=>[
                'Oui' => true,
                'non' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Criteres::class,
        ]);
    }
}

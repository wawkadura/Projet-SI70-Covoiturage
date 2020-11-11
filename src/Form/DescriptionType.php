<?php

namespace App\Form;

use App\Entity\Description;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('miniBio', TextareaType::class )
            ->add('voyageAvecFumeur')
            ->add('voyagerAvecMusique')
            ->add('voyagerAvecAnimaux')
            ->add('centreInterets',TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}

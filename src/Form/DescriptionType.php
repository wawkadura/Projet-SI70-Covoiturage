<?php

namespace App\Form;

use App\Entity\Description;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('miniBio', TextareaType::class, array('required' => false) )
            ->add('voyageAvecFumeur', ChoiceType::class, [
                'choices' => [
                  'Oui' => true,
                  'Non' => false,  
                ],
            ])
            ->add('voyagerAvecMusique', ChoiceType::class, [
                'choices' => [
                  'Oui' => true,
                  'Non' => false,  
                ],
            ])
            ->add('voyagerAvecAnimaux', ChoiceType::class, [
                'choices' => [
                  'Oui' => true,
                  'Non' => false,  
                ],
            ])
            ->add('centreInterets',TextareaType::class, array('required' => false) )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}

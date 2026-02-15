<?php

namespace App\Form;

use App\Entity\Boat;
use App\Entity\Formula;
use App\Entity\Rental;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('price')
            ->add('boats', EntityType::class, [
                'class' => Boat::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes pour une meilleure UX
            ])
            ->add('rentals', EntityType::class, [
                'class' => Rental::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formula::class,
        ]);
    }
}

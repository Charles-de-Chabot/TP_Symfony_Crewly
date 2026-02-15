<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Boat;
use App\Entity\Formula;
use App\Entity\Model;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BoatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('picture', FileType::class, [
                'label' => 'Image du bateau',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, WEBP)',
                    ])
                ],
            ])
            ->add('maxUser')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('isActive')
            ->add('boatLength')
            ->add('boatWidth')
            ->add('boatDraught')
            ->add('cabineNumber')
            ->add('bedNumber')
            ->add('fuel')
            ->add('powerEngine')
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'id',
            ])
            ->add('model', EntityType::class, [
                'class' => Model::class,
                'choice_label' => 'id',
            ])
            ->add('adress', EntityType::class, [
                'class' => Adress::class,
                'choice_label' => 'id',
            ])
            ->add('formula', EntityType::class, [
                'class' => Formula::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boat::class,
        ]);
    }
}

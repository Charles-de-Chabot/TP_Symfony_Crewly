<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Boat;
use App\Entity\Formula;
use App\Entity\Model;
use App\Entity\Type;
use App\Form\AdressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Formulaire de création/édition d'un Bateau.
 *
 * CONCEPTS CLÉS :
 * - FileType avec 'mapped' => false : Gestion manuelle de l'upload (le fichier n'est pas stocké directement en base)
 * - EntityType : Liste déroulante alimentée par une entité Doctrine (Type, Model)
 * - Imbrication : Utilisation de AdressType pour gérer l'adresse du bateau dans le même formulaire
 */
class BoatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du bateau',
                'attr' => ['placeholder' => 'Ex: Le Grand Bleu']
            ])
            ->add('description', null, [
                'label' => 'Description détaillée',
                'attr' => ['placeholder' => 'Décrivez le bateau, ses équipements, son histoire...']
            ])
            // Champ fichier non lié à l'entité ('mapped' => false)
            // On doit gérer l'upload manuellement dans le contrôleur ou un service
            // Les contraintes sont définies ici car l'entité n'a pas de propriété "file"
            ->add('picture', FileType::class, [
                'label' => 'Image principale',
                'help' => 'Format accepté : JPG, PNG, WEBP (Max 5Mo)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        mimeTypesMessage: 'Veuillez télécharger une image valide (JPG, PNG, WEBP)',
                    )
                ],
            ])
            ->add('maxUser', null, [
                'label' => 'Capacité (personnes)',
                'attr' => ['placeholder' => 'Ex: 8']
            ])
            ->add('isActive', null, [
                'label' => 'En ligne',
                'help' => 'Cochez pour rendre le bateau visible sur le site',
            ])
            ->add('boatLength', null, [
                'label' => 'Longueur (m)',
                'attr' => ['placeholder' => 'Ex: 12.50']
            ])
            ->add('boatWidth', null, [
                'label' => 'Largeur (m)',
                'attr' => ['placeholder' => 'Ex: 4.20']
            ])
            ->add('boatDraught', null, [
                'label' => 'Tirant d\'eau (m)',
                'attr' => ['placeholder' => 'Ex: 1.80']
            ])
            ->add('cabineNumber', null, [
                'label' => 'Nombre de cabines',
                'attr' => ['placeholder' => 'Ex: 3']
            ])
            ->add('bedNumber', null, [
                'label' => 'Nombre de lits',
                'attr' => ['placeholder' => 'Ex: 6']
            ])
            ->add('fuel', null, [
                'label' => 'Type de carburant',
                'attr' => ['placeholder' => 'Ex: Diesel, Essence...']
            ])
            ->add('powerEngine', null, [
                'label' => 'Puissance moteur (CV)',
                'attr' => ['placeholder' => 'Ex: 350']
            ])
            // Sélection d'une entité liée (ManyToOne)
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'label',
                'label' => 'Type de bateau',
                'placeholder' => 'Sélectionnez un type',
            ])
            ->add('model', EntityType::class, [
                'class' => Model::class,
                'choice_label' => 'label',
                'label' => 'Modèle',
                'placeholder' => 'Sélectionnez un modèle',
            ])
            // Imbrication du formulaire d'adresse
            // Les champs de AdressType seront affichés ici
            ->add('adress', AdressType::class, [
                'label' => false,
            ])
            ->add('formula', EntityType::class, [
                'class' => Formula::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Formules de location',
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

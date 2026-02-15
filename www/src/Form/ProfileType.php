<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Formulaire d'édition du profil utilisateur.
 *
 * CONCEPT CLÉ :
 * - 'property_path' : Permet de mapper un champ du formulaire directement vers une propriété
 *   d'un sous-objet (ex: 'adress.city' modifie la ville de l'adresse de l'utilisateur).
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'firstName',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'Votre Prénom',
                    'autocomplete' => 'given-name',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le pénom est obligatoire.'),
                    new Assert\Length(max: 50, maxMessage: 'Le pénom ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])

            ->add('lastName', TextType::class, [
                'label' => 'lastName',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'Votre Nom',
                    'autocomplete' => 'family-name',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le nom de famille est obligatoire.'),
                    new Assert\Length(max: 50, maxMessage: 'Le nom de famille ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'Votre adresse email',
                    'autocomplete' => 'email',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'L\'email est obligatoire.'),
                    new Assert\Email(message: 'L\'email n\'est pas valide'),
                    new Assert\Length(max: 180, maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'phoneNumber',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'Votre numéro de téléphone',
                    'autocomplete' => 'tel',
                ],
                'constraints' => [
                    new Assert\Length(max: 50, maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            // Ici, on ne charge pas AdressType, on mappe manuellement les champs
            // vers l'objet Adress lié à l'User via property_path
            ->add('houseNumber', TextType::class, [
                'label' => 'Numéro de rue',
                'property_path' => 'adress.houseNumber',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'ex: 12 bis',
                ],
                'constraints' => [
                    new Assert\Length(max: 20, maxMessage: 'Le numéro ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Nom de la rue',
                'property_path' => 'adress.streetName',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'ex: Rue de la Paix',
                    'autocomplete' => 'street-address',
                ],
                'constraints' => [
                    new Assert\Length(max: 255, maxMessage: 'Le nom de la rue ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Code postal',
                'property_path' => 'adress.postcode',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => '75000',
                    'autocomplete' => 'postal-code',
                ],
                'constraints' => [
                    new Assert\Length(max: 15, maxMessage: 'Le code postal ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'property_path' => 'adress.city',
                'attr' => [
                    'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    'placeholder' => 'ex: Paris',
                    'autocomplete' => 'address-level2',
                ],
                'constraints' => [
                    new Assert\Length(max: 255, maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity(
                    fields: 'email',
                    message: 'Cet email est déjà utilisé.',
                ),
            ],
        ]);
    }
}

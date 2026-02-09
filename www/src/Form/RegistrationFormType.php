<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Prénom
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Votre prénom',
                    'autocomplete' => 'given-name',
                ],
            ])
            // Nom
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Votre nom',
                    'autocomplete' => 'family-name',
                ],
            ])
            // Numéro de téléphone
            ->add('phoneNumber', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => '06 00 00 00 00',
                    'autocomplete' => 'tel',
                ],
            ])
            // Adresse
            ->add('adress', AdressType::class, [
                'label' => 'Votre adresse postale :',
            ])
            // email
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'votre@email.com',
                    'autocomplete' => 'email',
                ],
            ])
            // Mot de passe
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-input',
                        'placeholder' => 'Minimum 6 caractères',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'class' => 'form-input',
                        'placeholder' => 'Répétez le mot de passe',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un mot de passe'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        max: 255
                    ),
                ],
            ])
            // Conditions d'utilisation
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation',
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter les conditions d\'utilisation.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
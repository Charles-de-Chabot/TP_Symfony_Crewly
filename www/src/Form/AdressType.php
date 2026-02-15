<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire réutilisable pour l'entité Adress.
 *
 * CONCEPT CLÉ :
 * - Ce formulaire n'est pas destiné à être utilisé seul, mais imbriqué
 *   dans d'autres formulaires (BoatType, ProfileType) via CollectionType ou directement.
 */
class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('houseNumber', TextType::class, [
                'label' => 'N° de rue',
                'attr' => ['placeholder' => '12 bis', 'class' => 'form-input']
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Nom de la rue',
                'attr' => ['placeholder' => 'rue de la Paix', 'class' => 'form-input']
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => '75000', 'class' => 'form-input']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Paris', 'class' => 'form-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
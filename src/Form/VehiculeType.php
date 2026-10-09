<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, ['label' => 'Marque', 'attr' => ['placeholder' => 'ex : Volkswagen']])
            ->add('modele', TextType::class, ['label' => 'Modèle', 'attr' => ['placeholder' => 'ex : Golf']])
            ->add('annee', IntegerType::class, [
                'label' => 'Année',
                'required' => false,
                'attr' => ['min' => Vehicule::ANNEE_MIN, 'placeholder' => 'ex : 2020'],
            ])
            ->add('couleur', TextType::class, ['label' => 'Couleur', 'required' => false])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => ['placeholder' => 'AB-123-CD', 'autocapitalize' => 'characters', 'maxlength' => 15],
            ])
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Nombre de places (conducteur inclus)',
                'attr' => ['min' => 2, 'max' => 9],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Vehicule::class]);
    }
}

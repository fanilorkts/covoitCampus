<?php

namespace App\Form;

use App\Entity\Trajets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class TrajetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('origine', null, ['label' => 'Départ'])
            ->add('destination', null, ['label' => 'Destination'])
            ->add('date_heure', DateTimeType::class, ['label' => 'Date et heure', 'widget' => 'single_text'])
            ->add('places_totales', IntegerType::class, ['label' => 'Places disponibles', 'attr' => ['min' => 1]])
            ->add('prix', MoneyType::class, ['label' => 'Prix par passager', 'currency' => 'EUR'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajets::class,
        ]);
    }

    
}
<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('biographie', TextareaType::class, [
                'required' => false,
                'label' => 'Biographie',
                'constraints' => [new Length(max: 1000)],
            ])
            ->add('centresInteret', TextType::class, [
                'required' => false,
                'label' => 'Centres d\'intérêt',
                'help' => 'Sépare-les par des virgules (ex : musique, sport, cinéma)',
                'constraints' => [new Length(max: 255)],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Utilisateurs::class]);
    }
}
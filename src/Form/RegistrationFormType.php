<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre nom'),
                    new Length(max: 100),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre prénom'),
                    new Length(max: 100),
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre date de naissance'),
                    new LessThan('today', message: 'La date de naissance doit être dans le passé'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                // non mappé : le mot de passe en clair n'est jamais mis dans l'entité,
                // le contrôleur le hache puis appelle setPassword() avec le hash
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un mot de passe'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: "Vous devez accepter les conditions d'utilisation."),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
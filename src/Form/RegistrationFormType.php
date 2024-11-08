<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('email')

            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', // Affiche un champ date sans sélecteur de date
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 200,
                    ]),
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
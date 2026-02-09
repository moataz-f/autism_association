<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom / اللقب',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom / الاسم',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail / البريد الإلكتروني',
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre@email.com']
            ])
            ->add('numtlf', TextType::class, [
                'label' => 'Téléphone / الهاتف',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '216...']
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe / كلمة المرور',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control', 'placeholder' => '********'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
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

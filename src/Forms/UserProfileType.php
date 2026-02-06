<?php

namespace App\Forms;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'الاسم',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nom', TextType::class, [
                'label' => 'اللقب',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'البريد الإلكتروني',
                'attr' => ['class' => 'form-control']
            ])
            ->add('numtlf', TextType::class, [
                'label' => 'رقم الهاتف',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: 216...']
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'كلمة المرور الجديدة (اختياري)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                'help' => 'اتركه فارغاً للحفاظ على كلمة المرور الحالية'
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

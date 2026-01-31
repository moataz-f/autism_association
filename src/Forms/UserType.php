<?php

namespace App\Forms;

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

class UserType extends AbstractType
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
                'attr' => ['class' => 'form-control']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'الصلاحيات (الأدوار)',
                'choices' => [
                    'مدير (Admin)' => 'ROLE_ADMIN',
                    'أخصائي (Specialist)' => 'ROLE_SPECIALIST',
                    'ولي أمر (Parent)' => 'ROLE_PARENT',
                    'موظف (Staff)' => 'ROLE_STAFF',
                    'متطوع (Volunteer)' => 'ROLE_VOLUNTEER',
                    'متبرع (Donor)' => 'ROLE_DONOR',
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-check']
            ]);

        if ($options['is_new']) {
            $builder->add('plainPassword', PasswordType::class, [
                'label' => 'كلمة المرور',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'يرجى إدخال كلمة المرور',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'يجب أن تتكون كلمة المرور من {{ limit }} أحرف على الأقل',
                        'max' => 4096,
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
        ]);
    }
}

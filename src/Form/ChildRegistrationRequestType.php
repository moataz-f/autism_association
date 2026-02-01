<?php

namespace App\Form;

use App\Entity\ChildRegistrationRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildRegistrationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'enfant / لقب الطفل',
                'attr' => ['class' => 'form-control', 'placeholder' => 'لقب الطفل']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom de l\'enfant / اسم الطفل',
                'attr' => ['class' => 'form-control', 'placeholder' => 'اسم الطفل']
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance / تاريخ الولادة',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('niveauAutisme', ChoiceType::class, [
                'label' => 'Niveau d\'autisme / مستوى التوحد',
                'choices' => [
                    'Léger / خفيف' => 'Léger',
                    'Modéré / متوسط' => 'Modéré',
                    'Sévère / شديد' => 'Sévère',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre / الجنس',
                'choices' => [
                    'Masculin / ذكر' => 'Masculin',
                    'Féminin / أنثى' => 'Féminin',
                ],
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChildRegistrationRequest::class,
        ]);
    }
}

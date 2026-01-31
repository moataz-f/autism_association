<?php

namespace App\Formss;

use App\Entity\DonationRequest;
use App\Entity\DonationCampaign;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DonationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'الأسم واللقب',
                'constraints' => [new NotBlank(['message' => 'يرجى إدخال الأسم'])],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'رقم الهاتف',
                'constraints' => [new NotBlank(['message' => 'يرجى إدخال رقم الهاتف'])],
            ])
            ->add('localisation', TextType::class, [
                'label' => 'الموقع / العنوان',
                'constraints' => [new NotBlank(['message' => 'يرجى إدخال الموقع'])],
            ])
            ->add('montant', NumberType::class, [
                'label' => 'المبلغ (د.ت)',
                'constraints' => [new NotBlank(['message' => 'يرجى إدخال المبلغ'])],
            ])
            ->add('campaign', EntityType::class, [
                'class' => DonationCampaign::class,
                'choice_label' => 'titre',
                'label' => 'الحملة',
                'placeholder' => 'اختر الحملة',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DonationRequest::class,
        ]);
    }
}

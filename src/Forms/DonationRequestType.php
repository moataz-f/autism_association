<?php

namespace App\Forms;

use App\Entity\DonationRequest;
use App\Entity\DonationCampaign;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
            ->add('type', ChoiceType::class, [
                'label' => 'نوع التبرع',
                'choices' => [
                    'مادي (نقدي)' => 'financier',
                    'عيني (مستلزمات)' => 'materiel',
                ],
                'expanded' => true,
                'attr' => ['class' => 'd-flex gap-3 mb-3'],
            ])
            ->add('descriptionMateriel', TextareaType::class, [
                'label' => 'وصف التبرع العيني',
                'required' => false,
                'attr' => ['placeholder' => 'مثال: كراسي، ألعاب، أدوات مدرسية...', 'rows' => 3],
            ])
            ->add('montant', NumberType::class, [
                'label' => 'المبلغ (د.ت)',
                'required' => false,
                'attr' => ['placeholder' => '0.00'],
            ])
            ->add('campaign', EntityType::class, [
                'class' => DonationCampaign::class,
                'choice_label' => 'titre',
                'label' => 'الحملة',
                'placeholder' => 'اختر الحملة',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DonationRequest::class,
            'constraints' => [
                new Callback([$this, 'validateDonation']),
            ],
        ]);
    }

    public function validateDonation($data, ExecutionContextInterface $context): void
    {
        if ($data->getType() === 'financier') {
            if (empty($data->getMontant())) {
                $context->buildViolation('يرجى إدخال المبلغ للتبرع المادي')
                    ->atPath('montant')
                    ->addViolation();
            }
        } elseif ($data->getType() === 'materiel') {
            if (empty($data->getDescriptionMateriel())) {
                $context->buildViolation('يرجى وصف التبرع العيني')
                    ->atPath('descriptionMateriel')
                    ->addViolation();
            }
        }
    }
}

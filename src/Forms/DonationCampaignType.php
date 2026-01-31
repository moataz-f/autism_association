<?php
// src/Form/DonationCampaignType.php
namespace App\Forms;

use App\Entity\DonationCampaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationCampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'عنوان الحملة',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'الوصف',
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('montantObjectif', MoneyType::class, [
                'label' => 'المبلغ المطلوب (دينار)',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control']
            ])
            ->add('montantCollecte', MoneyType::class, [
                'label' => 'المبلغ المجموع (دينار)',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'تاريخ البداية',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'تاريخ النهاية',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'نشطة',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('principale', CheckboxType::class, [
                'label' => 'حملة رئيسية',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'help' => 'سيتم عرضها في الصفحة الرئيسية'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DonationCampaign::class,
        ]);
    }
}
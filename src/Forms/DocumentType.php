<?php

namespace App\Forms;

use App\Entity\BeneficiaireDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'نوع الملف',
                'choices' => [
                    'طبي' => 'Medical',
                    'مدرسي' => 'Scolaire',
                    'إداري' => 'Administratif',
                    'أخرى' => 'Autre',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('file', FileType::class, [
                'label' => 'الملف (PDF, Image)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'حجم الملف كبير جداً (الأقصى 5 ميجابايت).',
                        'mimeTypesMessage' => 'يرجى تحميل ملف بصيغة PDF أو صورة صالحة (JPG, PNG)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BeneficiaireDocument::class,
        ]);
    }
}

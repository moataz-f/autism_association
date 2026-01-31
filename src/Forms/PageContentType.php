<?php
// src/Form/PageContentType.php
namespace App\Forms;

use App\Entity\PageContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PageContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sectionKey', ChoiceType::class, [
                'label' => 'القسم',
                'choices' => [
                    'القسم الرئيسي (Hero)' => 'hero',
                    'من نحن' => 'about',
                    'الإحصائيات' => 'stats',
                    'اتصل بنا' => 'contact',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('titre', TextType::class, [
                'label' => 'العنوان',
                'attr' => ['class' => 'form-control']
            ])
            ->add('sousTitre', TextType::class, [
                'label' => 'العنوان الفرعي',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'الوصف',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('videoFile', VichFileType::class, [
                'label' => 'فيديو الخلفية (MP4)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'حذف الفيديو',
                'download_uri' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('imageFile', VichFileType::class, [
                'label' => 'صورة',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'حذف الصورة',
                'download_uri' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('buttonText', TextType::class, [
                'label' => 'نص الزر',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('buttonLink', TextType::class, [
                'label' => 'رابط الزر',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'نشط',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageContent::class,
        ]);
    }
}
<?php
// src/Form/GalleryImageType.php
namespace App\Forms;

use App\Entity\GalleryImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GalleryImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'العنوان',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'الوصف',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'الصورة',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'حذف الصورة',
                'download_uri' => true,
                'image_uri' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'الترتيب',
                'attr' => ['class' => 'form-control'],
                'help' => 'رقم الترتيب في المعرض (1, 2, 3...)'
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
            'data_class' => GalleryImage::class,
        ]);
    }
}
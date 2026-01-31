<?php

namespace App\Forms;

use App\Entity\Actualite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ActualiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'العنوان',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'وصف قصير',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2]
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'المحتوى التفصيلي',
                'attr' => ['class' => 'form-control', 'rows' => 10]
            ])
            ->add('imageFile', VichFileType::class, [
                'label' => 'الصورة الرئيسية',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'نشط',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actualite::class,
        ]);
    }
}

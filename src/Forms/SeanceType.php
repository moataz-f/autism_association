<?php

namespace App\Forms;

use App\Entity\Beneficiaire;
use App\Entity\Seance;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'عنوان الحصة',
                'attr' => ['class' => 'form-control', 'placeholder' => 'مثال: حصة تخاطب فردية']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'نوع الحصة',
                'choices' => [
                    'فردية' => 'Individuelle',
                    'جماعية' => 'Collective',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('date', DateType::class, [
                'label' => 'التاريخ',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('heureDebut', TimeType::class, [
                'label' => 'وقت البدء',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('heureFin', TimeType::class, [
                'label' => 'وقت الانتهاء',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('specialiste', EntityType::class, [
                'label' => 'الأخصائي المسؤول',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_SPECIALIST%')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => function(User $user) {
                    return $user->getPrenom() . ' ' . $user->getNom();
                },
                'attr' => ['class' => 'form-select']
            ])
            ->add('beneficiaires', EntityType::class, [
                'label' => 'الأطفال المشاركون',
                'class' => Beneficiaire::class,
                'multiple' => true,
                'expanded' => false,
                'choice_label' => function(Beneficiaire $b) {
                    return $b->getPrenom() . ' ' . $b->getNom();
                },
                'attr' => ['class' => 'form-select select2', 'data-placeholder' => 'اختر الأطفال']
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'الحالة',
                'choices' => [
                    'مبرمجة' => 'Planned',
                    'تمت' => 'Completed',
                    'ملغاة' => 'Cancelled',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'ملاحظات',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}

<?php
// src/Form/ActiviteType.php
namespace App\Formss;

use App\Entity\Activite;
use App\Entity\Beneficiaire;
use App\Entity\Personnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'Activité',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de Début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de Fin',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'Activité',
                'choices' => [
                    'Éducative' => 'Éducative',
                    'Thérapeutique' => 'Thérapeutique',
                    'Sociale' => 'Sociale',
                    'Sportive' => 'Sportive',
                    'Artistique' => 'Artistique'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('personnel', EntityType::class, [
                'label' => 'Responsable',
                'class' => Personnel::class,
                'choice_label' => function(Personnel $personnel) {
                    return $personnel->getNom() . ' ' . $personnel->getPrenom() . ' (' . $personnel->getRole() . ')';
                },
                'required' => false,
                'attr' => ['class' => 'form-select']
            ])
            ->add('beneficiaires', EntityType::class, [
                'label' => 'Bénéficiaires',
                'class' => Beneficiaire::class,
                'choice_label' => function(Beneficiaire $beneficiaire) {
                    return $beneficiaire->getNom() . ' ' . $beneficiaire->getPrenom();
                },
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-select', 'size' => 5]
            ])
            ->add('capaciteMax', IntegerType::class, [
                'label' => 'Capacité Maximale',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Active' => true,
                    'Inactive' => false
                ],
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}
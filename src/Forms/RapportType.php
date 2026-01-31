<?php
// src/Form/RapportType.php
namespace App\Formss;

use App\Entity\Beneficiaire;
use App\Entity\Rapport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('beneficiaire', EntityType::class, [
                'label' => 'Bénéficiaire',
                'class' => Beneficiaire::class,
                'choice_label' => function(Beneficiaire $beneficiaire) {
                    return $beneficiaire->getNom() . ' ' . $beneficiaire->getPrenom();
                },
                'attr' => ['class' => 'form-select']
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de Rapport',
                'choices' => [
                    'Évaluation' => 'Évaluation',
                    'Suivi' => 'Suivi',
                    'Médical' => 'Médical',
                    'Comportemental' => 'Comportemental',
                    'Autre' => 'Autre'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu du Rapport',
                'attr' => ['class' => 'form-control', 'rows' => 10]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
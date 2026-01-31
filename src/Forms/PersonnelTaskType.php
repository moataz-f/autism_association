<?php

namespace App\Forms;

use App\Entity\Personnel;
use App\Entity\PersonnelEvent;
use App\Entity\PersonnelTask;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonnelTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Préparer le rapport mensuel']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('dueDate', DateTimeType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'PENDING',
                    'Terminé' => 'COMPLETED'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('personnel', EntityType::class, [
                'class' => Personnel::class,
                'label' => 'Assigner à',
                'choice_label' => function(Personnel $personnel) {
                    return $personnel->getNom() . ' ' . $personnel->getPrenom() . ' (' . $personnel->getRole() . ')';
                },
                'attr' => ['class' => 'form-select']
            ])
            ->add('event', EntityType::class, [
                'class' => PersonnelEvent::class,
                'label' => 'Événement lié (Optionnel)',
                'required' => false,
                'placeholder' => 'Aucun événement',
                'choice_label' => 'title',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonnelTask::class,
        ]);
    }
}

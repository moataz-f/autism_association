<?php
// src/Form/PersonnelType.php
namespace App\Formss;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Éducateur' => 'Éducateur',
                    'Psychologue' => 'Psychologue',
                    'Orthophoniste' => 'Orthophoniste',
                    'Administrateur' => 'Administrateur',
                    'Autre' => 'Autre'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control', 'placeholder' => '12345678']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateEmbauche', DateType::class, [
                'label' => 'Date d\'Embauche',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('specialisation', TextareaType::class, [
                'label' => 'Spécialisation',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false
                ],
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }
}

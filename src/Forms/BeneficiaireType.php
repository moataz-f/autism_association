<?php
// src/Form/BeneficiaireType.php
namespace App\Form;

use App\Entity\Beneficiaire;
use App\Entity\Famille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeneficiaireType extends AbstractType
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
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Masculin' => 'Masculin',
                    'Féminin' => 'Féminin'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '12345678']
            ])
            ->add('niveauAutisme', ChoiceType::class, [
                'label' => 'Niveau d\'Autisme',
                'choices' => [
                    'Léger' => 'Léger',
                    'Modéré' => 'Modéré',
                    'Sévère' => 'Sévère'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('diagnostic', TextareaType::class, [
                'label' => 'Diagnostic',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('famille', EntityType::class, [
                'label' => 'Famille',
                'class' => Famille::class,
                'choice_label' => function(Famille $famille) {
                    return $famille->getNomResponsable() . ' ' . $famille->getPrenomResponsable();
                },
                'attr' => ['class' => 'form-select']
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
            'data_class' => Beneficiaire::class,
        ]);
    }
}

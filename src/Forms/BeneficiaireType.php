<?php
// src/Form/BeneficiaireType.php
namespace App\Forms;

use App\Entity\Beneficiaire;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class BeneficiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'اللقب (Nom)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'الاسم (Prénom)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'تاريخ الولادة (Date de Naissance)',
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'form-control']
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'الجنس (Genre)',
                'choices' => [
                    'ذكر (Masculin)' => 'Masculin',
                    'أنثى (Féminin)' => 'Féminin'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'العنوان (Adresse)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'الهاتف (Téléphone)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '12345678']
            ])
            ->add('niveauAutisme', ChoiceType::class, [
                'label' => 'مستوى التوحد (Niveau d’Autisme)',
                'choices' => [
                    'خفيف (Léger)' => 'Léger',
                    'متوسط (Modéré)' => 'Modéré',
                    'شديد (Sévère)' => 'Sévère'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('diagnostic', TextareaType::class, [
                'label' => 'التشخيص (Diagnostic)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('parents', EntityType::class, [
                'label' => 'الأولياء المرتبطون (Parents (Utilisateurs))',
                'class' => User::class,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_PARENT%')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => function(User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' (' . $user->getEmail() . ')';
                },
                'attr' => ['class' => 'form-select select2']
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'الحالة (Statut)',
                'choices' => [
                    'نشط (Actif)' => true,
                    'غير نشط (Inactif)' => false
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

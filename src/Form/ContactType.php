<?php

namespace App\Form;

use App\Entity\ContactRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom / Prénom *',
                'attr' => ['placeholder' => 'Jean Dupont'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'attr' => ['placeholder' => 'jean@exemple.fr'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => '06 12 34 56 78'],
            ])
            ->add('projectType', ChoiceType::class, [
                'label' => 'Type de projet *',
                'choices' => [
                    'Site vitrine' => 'site-vitrine',
                    'Site professionnel' => 'site-pro',
                    'Application mobile' => 'app-mobile',
                    'Solution restaurant' => 'solution-restaurant',
                    'Maintenance' => 'maintenance',
                    'Autre' => 'autre',
                ],
            ])
            ->add('estimatedBudget', ChoiceType::class, [
                'label' => 'Budget estimé',
                'required' => false,
                'choices' => [
                    'Moins de 2000€' => '< 2000',
                    '2000€ - 5000€' => '2000-5000',
                    '5000€ - 10000€' => '5000-10000',
                    'Plus de 10000€' => '> 10000',
                    'Je ne sais pas' => 'unknown',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message *',
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Décrivez votre projet en quelques lignes...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class,
        ]);
    }
}

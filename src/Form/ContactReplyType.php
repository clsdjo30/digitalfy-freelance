<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de réponse à une demande de contact
 *
 * Ce formulaire permet à l'administrateur de répondre directement
 * à une demande de contact depuis le back-office EasyAdmin.
 */
class ContactReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ "Sujet" - sera utilisé comme objet de l'email
            ->add('subject', TextType::class, [
                'label' => 'Sujet de la réponse',
                'attr' => [
                    'placeholder' => 'Ex: Réponse à votre demande de projet',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le sujet ne peut pas être vide'
                    ]),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 200,
                        'minMessage' => 'Le sujet doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le sujet ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])

            // Champ "Réponse" - corps de l'email
            ->add('reply', TextareaType::class, [
                'label' => 'Votre réponse',
                'attr' => [
                    'rows' => 10,
                    'placeholder' => 'Rédigez votre réponse au client...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La réponse ne peut pas être vide'
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La réponse doit contenir au moins {{ limit }} caractères'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Ce formulaire n'est pas lié à une entité Doctrine
            'data_class' => null,
            // Configuration explicite du CSRF
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'contact_reply',
        ]);
    }

    /**
     * Définit le préfixe du nom du formulaire
     * Cela aide à générer un ID de token CSRF unique
     */
    public function getBlockPrefix(): string
    {
        return 'contact_reply';
    }
}

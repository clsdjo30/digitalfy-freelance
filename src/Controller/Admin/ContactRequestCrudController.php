<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de contact')
            ->setEntityLabelInPlural('Demandes de contact')
            ->setDefaultSort(['submittedAt' => 'DESC'])
            ->setSearchFields(['name', 'email', 'phone', 'message']);
    }

    /**
     * Configuration des actions disponibles pour les demandes de contact
     *
     * On remplace l'action "Éditer" par une action personnalisée "Répondre"
     * qui redirige vers le formulaire de réponse par email.
     */
    public function configureActions(Actions $actions): Actions
    {
        // Création d'une action personnalisée "Répondre"
        $replyAction = Action::new('reply', 'Répondre', 'fa fa-reply')
            // Route vers le contrôleur ContactReplyController
            ->linkToRoute('admin_contact_reply', function (ContactRequest $entity) {
                return ['id' => $entity->getId()];
            })
            // Classe CSS pour styliser le bouton
            ->addCssClass('btn btn-primary')
            // Afficher l'action dans la liste (index) et en détail
            ->displayIf(static fn (ContactRequest $entity) => true);

        return $actions
            // Désactiver l'action "Éditer" par défaut
            ->disable(Action::EDIT)
            // Désactiver l'action "Nouveau" (les demandes viennent du front)
            ->disable(Action::NEW)
            // Ajouter l'action "Détail" dans la page d'index (pour voir toutes les infos)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // Ajouter l'action "Répondre" dans la page d'index
            ->add(Crud::PAGE_INDEX, $replyAction)
            // Ajouter l'action "Répondre" dans la page de détail
            ->add(Crud::PAGE_DETAIL, $replyAction)
            // Réorganiser l'ordre des actions dans l'index
            ->reorder(Crud::PAGE_INDEX, [
                Action::DETAIL,
                'reply', // Notre action personnalisée
                Action::DELETE,
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            EmailField::new('email', 'Email'),
            TextField::new('phone', 'Téléphone')
                ->hideOnIndex(),
            TextField::new('projectType', 'Type de projet'),
            TextField::new('estimatedBudget', 'Budget estimé')
                ->hideOnIndex(),
            TextareaField::new('message', 'Message')
                ->hideOnIndex(),
            DateTimeField::new('submittedAt', 'Date de soumission')
                ->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Nouveau' => 'new',
                    'En cours' => 'in_progress',
                    'Clôturé' => 'closed',
                ])
                ->renderAsBadges([
                    'new' => 'info',
                    'in_progress' => 'warning',
                    'closed' => 'success',
                ]),
            TextareaField::new('notes', 'Notes internes')
                ->setHelp('Notes visibles uniquement dans l\'admin')
                ->hideOnIndex(),
        ];
    }
}

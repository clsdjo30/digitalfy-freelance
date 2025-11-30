<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use App\Repository\ConversationMessageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactRequestCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ConversationMessageRepository $conversationMessageRepository
    ) {
    }

    /**
     * Getter public pour accéder au repository depuis les templates
     */
    public function getConversationMessageRepository(): ConversationMessageRepository
    {
        return $this->conversationMessageRepository;
    }

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
            ->setSearchFields(['name', 'email', 'phone', 'message'])
            // Template personnalisé pour afficher la conversation
            ->overrideTemplate('crud/detail', 'admin/contact_request/detail.html.twig');
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
        $fields = [
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

        // Ajouter le champ "Messages" à la fin uniquement dans l'index
        if ($pageName === Crud::PAGE_INDEX) {
            $messageRepository = $this->conversationMessageRepository;

            // Créer un champ virtuel en réutilisant 'id' mais en changeant le label
            $messagesField = NumberField::new('id')
                ->setLabel('Messages')
                ->formatValue(function ($value, ContactRequest $entity) use ($messageRepository) {
                    $unreadCount = $messageRepository->countUnreadByContactRequest($entity);
                    $messages = $messageRepository->findByContactRequest($entity);
                    $totalCount = count($messages);

                    if ($unreadCount > 0) {
                        return sprintf(
                            '<span class="badge bg-danger">%d nouveau(x)</span> <small class="text-muted">(%d total)</small>',
                            $unreadCount,
                            $totalCount
                        );
                    } elseif ($totalCount > 0) {
                        return sprintf('<small class="text-muted">%d message(s)</small>', $totalCount);
                    }

                    return '<small class="text-muted">Aucun</small>';
                })
                ->setSortable(false);

            // Insérer le champ Messages après le statut
            array_splice($fields, 8, 0, [$messagesField]);
        }

        return $fields;
    }

}

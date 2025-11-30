<?php

namespace App\EventSubscriber;

use App\Controller\Admin\ContactRequestCrudController;
use App\Entity\ContactRequest;
use App\Repository\ConversationMessageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

/**
 * Event Subscriber pour injecter les messages de conversation
 * dans la page de détail des demandes de contact
 */
class ContactRequestDetailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ConversationMessageRepository $conversationMessageRepository,
        private readonly Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => ['injectConversationMessages'],
        ];
    }

    public function injectConversationMessages(BeforeCrudActionEvent $event): void
    {
        $context = $event->getAdminContext();

        // Vérifier que c'est bien le controller ContactRequest et l'action detail
        if (!$context->getCrud() || $context->getCrud()->getControllerFqcn() !== ContactRequestCrudController::class) {
            return;
        }

        if ($context->getCrud()->getCurrentAction() !== 'detail') {
            return;
        }

        // Récupérer l'entité ContactRequest
        $entity = $context->getEntity();
        if (!$entity || !($entity->getInstance() instanceof ContactRequest)) {
            return;
        }

        /** @var ContactRequest $contactRequest */
        $contactRequest = $entity->getInstance();

        // Récupérer les messages de conversation
        $messages = $this->conversationMessageRepository->findByContactRequest($contactRequest);

        // Marquer les messages reçus comme lus
        if (!empty($messages)) {
            $this->conversationMessageRepository->markAllAsReadForContactRequest($contactRequest);
        }

        // Injecter les variables dans le contexte Twig global
        $this->twig->addGlobal('messages', $messages);
        $this->twig->addGlobal('contactRequest', $contactRequest);
    }
}

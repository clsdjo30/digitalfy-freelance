<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use App\Form\ContactReplyType;
use App\Service\ContactReplyMailer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour gérer les réponses aux demandes de contact
 *
 * Ce contrôleur permet aux administrateurs de répondre directement
 * aux demandes de contact depuis le back-office EasyAdmin.
 */
#[Route('/admin/contact-reply')]
#[IsGranted('ROLE_ADMIN')]
class ContactReplyController extends AbstractController
{
    public function __construct(
        private readonly ContactReplyMailer $contactReplyMailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    /**
     * Page de réponse à une demande de contact
     *
     * Cette action affiche les détails de la demande de contact
     * et un formulaire permettant d'y répondre par email.
     *
     * @param ContactRequest $contactRequest La demande à laquelle répondre
     * @param Request $request La requête HTTP
     *
     * @return Response La page de réponse ou redirection après envoi
     */
    #[Route('/{id}', name: 'admin_contact_reply', methods: ['GET', 'POST'])]
    public function reply(ContactRequest $contactRequest, Request $request): Response
    {
        // Création du formulaire de réponse avec des valeurs par défaut
        $form = $this->createForm(ContactReplyType::class, [
            // Sujet pré-rempli basé sur le type de projet
            'subject' => sprintf(
                'Réponse à votre demande de %s',
                strtolower($contactRequest->getProjectType())
            ),
        ]);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $formData = $form->getData();
            $subject = $formData['subject'];
            $replyContent = $formData['reply'];

            try {
                // Envoi de l'email via le service
                $this->contactReplyMailer->sendReply(
                    $contactRequest,
                    $subject,
                    $replyContent
                );

                // Mise à jour du statut de la demande à "in_progress"
                // si elle était encore à "new"
                if ($contactRequest->getStatus() === 'new') {
                    $contactRequest->setStatus('in_progress');
                }

                // Ajout d'une note interne avec la date de réponse
                $currentNote = $contactRequest->getNotes() ?? '';
                $replyNote = sprintf(
                    "[%s] Réponse envoyée par email\nSujet: %s\n\n%s",
                    (new \DateTime())->format('d/m/Y H:i'),
                    $subject,
                    $currentNote
                );
                $contactRequest->setNotes($replyNote);

                // Sauvegarde en base de données
                $this->entityManager->flush();

                // Message de succès
                $this->addFlash('success', sprintf(
                    'Votre réponse a été envoyée avec succès à %s (%s).',
                    $contactRequest->getName(),
                    $contactRequest->getEmail()
                ));

                // Redirection vers la liste des demandes de contact dans EasyAdmin
                // Utilisation de AdminUrlGenerator pour générer l'URL correcte
                $url = $this->adminUrlGenerator
                    ->setController(ContactRequestCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl();

                return $this->redirect($url);

            } catch (TransportExceptionInterface $e) {
                // En cas d'erreur d'envoi, affichage d'un message d'erreur
                $this->addFlash('danger', sprintf(
                    'Erreur lors de l\'envoi de l\'email : %s',
                    $e->getMessage()
                ));
            }
        }

        // Affichage de la page de réponse avec le formulaire
        return $this->render('admin/contact_reply/reply.html.twig', [
            'contactRequest' => $contactRequest,
            'form' => $form,
        ]);
    }
}

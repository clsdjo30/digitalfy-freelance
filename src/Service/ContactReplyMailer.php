<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Entity\ConversationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Service d'envoi d'emails de réponse aux demandes de contact
 *
 * Ce service gère l'envoi d'emails de réponse depuis le back-office
 * vers les clients qui ont soumis une demande de contact.
 * Il sauvegarde également automatiquement tous les messages envoyés
 * dans la base de données pour le suivi de conversation.
 */
class ContactReplyMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Envoie un email de réponse à une demande de contact
     * et sauvegarde le message dans la base de données
     *
     * @param ContactRequest $contactRequest La demande de contact à laquelle on répond
     * @param string $subject Le sujet de l'email de réponse
     * @param string $replyContent Le contenu de la réponse
     *
     * @return ConversationMessage Le message sauvegardé
     *
     * @throws TransportExceptionInterface Si une erreur survient lors de l'envoi
     */
    public function sendReply(ContactRequest $contactRequest, string $subject, string $replyContent): ConversationMessage
    {
        try {
            // Génération d'un Message-ID unique pour tracer les réponses
            // Format: msg_uniqueid.requestId@digitalfy.fr (sans chevrons, ajoutés automatiquement)
            $messageId = sprintf(
                '%s.%s@digitalfy.fr',
                uniqid('msg_', true),
                $contactRequest->getId()
            );

            // Construction de l'email avec template Twig
            $email = (new TemplatedEmail())
                // Expéditeur : l'adresse email configurée de Digitalfy (IONOS)
                ->from(new Address('contact@digitalfy.fr', 'Digitalfy - Création Web & Mobile'))

                // Destinataire : le client qui a soumis la demande
                ->to(new Address($contactRequest->getEmail(), $contactRequest->getName()))

                // Sujet de l'email
                ->subject($subject)

                // Template Twig pour le rendu HTML et texte
                ->htmlTemplate('emails/contact_reply.html.twig')

                // Variables passées au template
                ->context([
                    'contactRequest' => $contactRequest,
                    'replyContent' => $replyContent,
                    'subject' => $subject,
                ]);

            // Ajout de headers personnalisés pour le suivi de conversation
            $headers = $email->getHeaders();
            // Utiliser addIdHeader pour Message-ID (crée un IdentificationHeader)
            $headers->addIdHeader('Message-ID', $messageId);
            $headers->addTextHeader('X-Digitalfy-Request-ID', (string) $contactRequest->getId());

            // Envoi de l'email via Symfony Mailer
            $this->mailer->send($email);

            // Sauvegarde du message dans la base de données
            $conversationMessage = new ConversationMessage();
            $conversationMessage->setContactRequest($contactRequest);
            $conversationMessage->setDirection('sent');
            $conversationMessage->setSender('contact@digitalfy.fr');
            $conversationMessage->setRecipient($contactRequest->getEmail());
            $conversationMessage->setSubject($subject);
            $conversationMessage->setContent($replyContent);
            // Sauvegarder le Message-ID avec les chevrons (format standard)
            $conversationMessage->setMessageId('<' . $messageId . '>');
            $conversationMessage->setSentAt(new \DateTime());
            $conversationMessage->setIsRead(true); // Les messages envoyés sont déjà "lus"

            $this->entityManager->persist($conversationMessage);
            $this->entityManager->flush();

            return $conversationMessage;
        } catch (TransportExceptionInterface $e) {
            // En cas d'erreur d'envoi, on relance l'exception
            // pour que le contrôleur puisse afficher un message d'erreur
            throw $e;
        }
    }

    /**
     * Envoie un email de test pour vérifier la configuration IONOS
     *
     * @param string $testEmail Email de destination pour le test
     *
     * @return bool True si l'email de test a été envoyé avec succès
     *
     * @throws TransportExceptionInterface Si une erreur survient lors de l'envoi
     */
    public function sendTestEmail(string $testEmail): bool
    {
        try {
            $email = (new TemplatedEmail())
                ->from(new Address('contact@digitalfy.fr', 'Digitalfy'))
                ->to($testEmail)
                ->subject('Test de configuration email - Digitalfy')
                ->htmlTemplate('emails/test_email.html.twig')
                ->context([
                    'testDate' => new \DateTime(),
                ]);

            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            throw $e;
        }
    }
}

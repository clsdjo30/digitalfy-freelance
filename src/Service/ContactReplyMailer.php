<?php

namespace App\Service;

use App\Entity\ContactRequest;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service d'envoi d'emails de réponse aux demandes de contact
 *
 * Ce service gère l'envoi d'emails de réponse depuis le back-office
 * vers les clients qui ont soumis une demande de contact.
 */
class ContactReplyMailer
{
    /**
     * @param MailerInterface $mailer Interface Symfony Mailer pour l'envoi d'emails
     */
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    /**
     * Envoie un email de réponse à une demande de contact
     *
     * @param ContactRequest $contactRequest La demande de contact à laquelle on répond
     * @param string $subject Le sujet de l'email de réponse
     * @param string $replyContent Le contenu de la réponse
     *
     * @return bool True si l'email a été envoyé avec succès, False sinon
     *
     * @throws TransportExceptionInterface Si une erreur survient lors de l'envoi
     */
    public function sendReply(ContactRequest $contactRequest, string $subject, string $replyContent): bool
    {
        try {
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

            // Envoi de l'email via Symfony Mailer
            $this->mailer->send($email);

            return true;
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

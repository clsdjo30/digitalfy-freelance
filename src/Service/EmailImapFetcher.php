<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Entity\ConversationMessage;
use App\Repository\ContactRequestRepository;
use App\Repository\ConversationMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de récupération d'emails via IMAP
 *
 * Ce service se connecte à la boîte email IONOS via IMAP,
 * récupère les nouveaux emails reçus, et les associe automatiquement
 * aux demandes de contact correspondantes.
 */
class EmailImapFetcher
{
    private string $imapHost;
    private string $imapUsername;
    private string $imapPassword;
    private int $imapPort;
    private bool $imapSsl;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContactRequestRepository $contactRequestRepository,
        private readonly ConversationMessageRepository $conversationMessageRepository,
        private readonly LoggerInterface $logger,
        string $imapHost,
        string $imapUsername,
        string $imapPassword,
        int $imapPort = 993,
        bool $imapSsl = true
    ) {
        $this->imapHost = $imapHost;
        $this->imapUsername = $imapUsername;
        $this->imapPassword = $imapPassword;
        $this->imapPort = $imapPort;
        $this->imapSsl = $imapSsl;
    }

    /**
     * Récupère les nouveaux emails de la boîte de réception
     *
     * @return int Nombre d'emails récupérés
     */
    public function fetchNewEmails(): int
    {
        // Vérification que l'extension IMAP est disponible
        if (!function_exists('imap_open')) {
            $this->logger->error('L\'extension PHP IMAP n\'est pas installée');
            throw new \RuntimeException('L\'extension PHP IMAP n\'est pas installée. Installez-la avec : apt-get install php-imap');
        }

        try {
            // Connexion IMAP
            $mailbox = $this->connectToMailbox();

            // Récupération des emails non lus
            $emailIds = imap_search($mailbox, 'UNSEEN');

            if (!$emailIds) {
                $this->logger->info('Aucun nouvel email à récupérer');
                imap_close($mailbox);
                return 0;
            }

            $count = 0;

            foreach ($emailIds as $emailId) {
                try {
                    if ($this->processEmail($mailbox, $emailId)) {
                        $count++;
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Erreur lors du traitement de l\'email #' . $emailId, [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Fermeture de la connexion IMAP
            imap_close($mailbox);

            $this->logger->info(sprintf('%d email(s) récupéré(s) et traité(s)', $count));

            return $count;

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des emails IMAP', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Établit la connexion IMAP
     *
     * @return resource
     */
    private function connectToMailbox()
    {
        $flags = $this->imapSsl ? '/imap/ssl' : '/imap';
        $mailboxString = sprintf(
            '{%s:%d%s}INBOX',
            $this->imapHost,
            $this->imapPort,
            $flags
        );

        $mailbox = @imap_open($mailboxString, $this->imapUsername, $this->imapPassword);

        if (!$mailbox) {
            $error = imap_last_error();
            throw new \RuntimeException('Impossible de se connecter à la boîte IMAP : ' . $error);
        }

        return $mailbox;
    }

    /**
     * Traite un email et le sauvegarde dans la base de données
     *
     * @param resource $mailbox
     * @param int $emailId
     * @return bool True si l'email a été traité avec succès
     */
    private function processEmail($mailbox, int $emailId): bool
    {
        // Récupération des headers
        $header = imap_headerinfo($mailbox, $emailId);
        if (!$header) {
            return false;
        }

        // Récupération du corps de l'email
        $body = $this->getEmailBody($mailbox, $emailId);

        // Extraction des informations
        $from = $this->extractEmail($header->from[0] ?? null);
        $to = $this->extractEmail($header->to[0] ?? null);
        $subject = $this->decodeHeader($header->subject ?? '');
        $date = isset($header->date) ? new \DateTime($header->date) : new \DateTime();
        $messageId = $header->message_id ?? null;
        $inReplyTo = $header->in_reply_to ?? null;
        $references = $header->references ?? null;

        // Tentative d'association à une demande de contact
        $contactRequest = $this->findContactRequest($from, $subject, $inReplyTo, $references, $to);

        if (!$contactRequest) {
            $this->logger->warning('Impossible d\'associer l\'email à une demande de contact', [
                'from' => $from,
                'subject' => $subject
            ]);
            return false;
        }

        // Vérification que le message n'existe pas déjà
        if ($messageId && $this->conversationMessageRepository->findByMessageId($messageId)) {
            $this->logger->info('Email déjà traité (Message-ID existe)', ['message_id' => $messageId]);
            return false;
        }

        // Création du message de conversation
        $conversationMessage = new ConversationMessage();
        $conversationMessage->setContactRequest($contactRequest);
        $conversationMessage->setDirection('received');
        $conversationMessage->setSender($from);
        $conversationMessage->setRecipient($to);
        $conversationMessage->setSubject($subject);
        $conversationMessage->setContent($body['text']);
        $conversationMessage->setHtmlContent($body['html']);
        $conversationMessage->setSentAt($date);
        $conversationMessage->setIsRead(false);
        $conversationMessage->setMessageId($messageId);
        $conversationMessage->setInReplyTo($inReplyTo);
        $conversationMessage->setEmailReferences($references);

        $this->entityManager->persist($conversationMessage);
        $this->entityManager->flush();

        $this->logger->info('Email sauvegardé', [
            'contact_request_id' => $contactRequest->getId(),
            'from' => $from,
            'subject' => $subject
        ]);

        return true;
    }

    /**
     * Recherche la demande de contact associée à un email
     */
    private function findContactRequest(
        string $from,
        string $subject,
        ?string $inReplyTo,
        ?string $references,
        string $to
    ): ?ContactRequest {
        // Méthode 1 : Via le header X-Digitalfy-Request-ID dans References/In-Reply-To
        if ($inReplyTo || $references) {
            $allRefs = trim(($inReplyTo ?? '') . ' ' . ($references ?? ''));
            if (preg_match('/msg_[^@]+\.(\d+)@digitalfy\.fr/', $allRefs, $matches)) {
                $requestId = (int) $matches[1];
                $contactRequest = $this->contactRequestRepository->find($requestId);
                if ($contactRequest && $contactRequest->getEmail() === $from) {
                    return $contactRequest;
                }
            }
        }

        // Méthode 2 : Via l'adresse email de l'expéditeur
        $contactRequests = $this->contactRequestRepository->findBy(['email' => $from], ['submittedAt' => 'DESC']);

        if (count($contactRequests) === 1) {
            return $contactRequests[0];
        }

        if (count($contactRequests) > 1) {
            // Si plusieurs demandes, prendre la plus récente qui a des messages
            foreach ($contactRequests as $contactRequest) {
                $messages = $this->conversationMessageRepository->findByContactRequest($contactRequest);
                if (count($messages) > 0) {
                    return $contactRequest;
                }
            }
            // Sinon, prendre la plus récente
            return $contactRequests[0];
        }

        return null;
    }

    /**
     * Extrait l'adresse email d'un objet stdClass IMAP
     */
    private function extractEmail($addressObj): string
    {
        if (!$addressObj) {
            return '';
        }

        $mailbox = $addressObj->mailbox ?? '';
        $host = $addressObj->host ?? '';

        return $mailbox && $host ? $mailbox . '@' . $host : '';
    }

    /**
     * Décode un header MIME encodé
     */
    private function decodeHeader(string $header): string
    {
        $decoded = imap_mime_header_decode($header);
        $result = '';

        foreach ($decoded as $part) {
            $charset = ($part->charset === 'default') ? 'UTF-8' : $part->charset;
            $result .= mb_convert_encoding($part->text, 'UTF-8', $charset);
        }

        return $result;
    }

    /**
     * Récupère le corps de l'email (texte et HTML)
     */
    private function getEmailBody($mailbox, int $emailId): array
    {
        $structure = imap_fetchstructure($mailbox, $emailId);
        $body = [
            'text' => '',
            'html' => ''
        ];

        if (!$structure) {
            return $body;
        }

        // Email simple (non-multipart)
        if (!isset($structure->parts)) {
            $bodyContent = imap_body($mailbox, $emailId);
            $body['text'] = $this->decodeBody($bodyContent, $structure->encoding ?? 0);
            return $body;
        }

        // Email multipart
        foreach ($structure->parts as $partIndex => $part) {
            $partNumber = $partIndex + 1;
            $partData = imap_fetchbody($mailbox, $emailId, (string) $partNumber);
            $decoded = $this->decodeBody($partData, $part->encoding ?? 0);

            // Type MIME
            $mimeType = $this->getMimeType($part);

            if ($mimeType === 'text/plain') {
                $body['text'] = $decoded;
            } elseif ($mimeType === 'text/html') {
                $body['html'] = $decoded;
            }
        }

        // Si pas de texte brut, extraire du HTML
        if (!$body['text'] && $body['html']) {
            $body['text'] = strip_tags($body['html']);
        }

        return $body;
    }

    /**
     * Décode le contenu d'un email selon son encodage
     */
    private function decodeBody(string $body, int $encoding): string
    {
        return match ($encoding) {
            1 => imap_8bit($body),           // 8BIT
            2 => imap_binary($body),         // BINARY
            3 => imap_base64($body),         // BASE64
            4 => quoted_printable_decode($body), // QUOTED-PRINTABLE
            default => $body,                // 7BIT ou autre
        };
    }

    /**
     * Détermine le type MIME d'une partie d'email
     */
    private function getMimeType($part): string
    {
        $primaryType = [
            0 => 'text',
            1 => 'multipart',
            2 => 'message',
            3 => 'application',
            4 => 'audio',
            5 => 'image',
            6 => 'video',
            7 => 'other'
        ];

        $type = $primaryType[$part->type ?? 0] ?? 'other';
        $subtype = strtolower($part->subtype ?? 'plain');

        return $type . '/' . $subtype;
    }
}

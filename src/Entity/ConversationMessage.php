<?php

namespace App\Entity;

use App\Repository\ConversationMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message dans une conversation client (email envoyé ou reçu)
 *
 * Cette entité stocke tous les échanges par email avec un client
 * suite à une demande de contact, permettant un suivi complet
 * de la conversation dans le back-office.
 */
#[ORM\Entity(repositoryClass: ConversationMessageRepository::class)]
#[ORM\Index(columns: ['sent_at'], name: 'idx_sent_at')]
#[ORM\Index(columns: ['direction'], name: 'idx_direction')]
class ConversationMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Demande de contact associée à ce message
     */
    #[ORM\ManyToOne(targetEntity: ContactRequest::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ContactRequest $contactRequest = null;

    /**
     * Direction du message : 'sent' (envoyé par nous) ou 'received' (reçu du client)
     */
    #[ORM\Column(length: 10)]
    private ?string $direction = null;

    /**
     * Adresse email de l'expéditeur
     */
    #[ORM\Column(length: 255)]
    private ?string $sender = null;

    /**
     * Adresse email du destinataire
     */
    #[ORM\Column(length: 255)]
    private ?string $recipient = null;

    /**
     * Sujet de l'email
     */
    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    /**
     * Contenu du message (texte brut)
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * Contenu HTML de l'email (optionnel)
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $htmlContent = null;

    /**
     * Date et heure d'envoi ou de réception
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sentAt = null;

    /**
     * Indique si le message a été lu par l'admin (pour les messages reçus)
     */
    #[ORM\Column]
    private bool $isRead = false;

    /**
     * ID du message email (Message-ID header) pour tracer les réponses
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $messageId = null;

    /**
     * ID du message auquel celui-ci répond (In-Reply-To header)
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $inReplyTo = null;

    /**
     * Chaîne de références (References header) pour suivre le fil de conversation
     * Renommé en emailReferences car "references" est un mot-clé réservé SQL
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $emailReferences = null;

    public function __construct()
    {
        $this->sentAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactRequest(): ?ContactRequest
    {
        return $this->contactRequest;
    }

    public function setContactRequest(?ContactRequest $contactRequest): static
    {
        $this->contactRequest = $contactRequest;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->direction === 'sent';
    }

    public function isReceived(): bool
    {
        return $this->direction === 'received';
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getHtmlContent(): ?string
    {
        return $this->htmlContent;
    }

    public function setHtmlContent(?string $htmlContent): static
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(?string $messageId): static
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getInReplyTo(): ?string
    {
        return $this->inReplyTo;
    }

    public function setInReplyTo(?string $inReplyTo): static
    {
        $this->inReplyTo = $inReplyTo;

        return $this;
    }

    public function getEmailReferences(): ?string
    {
        return $this->emailReferences;
    }

    public function setEmailReferences(?string $emailReferences): static
    {
        $this->emailReferences = $emailReferences;

        return $this;
    }

    /**
     * Retourne un extrait court du contenu (pour l'affichage dans les listes)
     */
    public function getExcerpt(int $length = 100): string
    {
        $content = strip_tags($this->content);
        if (mb_strlen($content) <= $length) {
            return $content;
        }

        return mb_substr($content, 0, $length) . '...';
    }
}

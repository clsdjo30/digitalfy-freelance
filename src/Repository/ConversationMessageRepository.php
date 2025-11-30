<?php

namespace App\Repository;

use App\Entity\ContactRequest;
use App\Entity\ConversationMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour les messages de conversation
 *
 * @extends ServiceEntityRepository<ConversationMessage>
 */
class ConversationMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationMessage::class);
    }

    /**
     * Récupère tous les messages d'une demande de contact, triés par date
     *
     * @param ContactRequest $contactRequest
     * @return ConversationMessage[]
     */
    public function findByContactRequest(ContactRequest $contactRequest): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.contactRequest = :contactRequest')
            ->setParameter('contactRequest', $contactRequest)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les messages non lus pour une demande de contact
     *
     * @param ContactRequest $contactRequest
     * @return ConversationMessage[]
     */
    public function findUnreadByContactRequest(ContactRequest $contactRequest): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.contactRequest = :contactRequest')
            ->andWhere('m.isRead = false')
            ->andWhere('m.direction = :direction')
            ->setParameter('contactRequest', $contactRequest)
            ->setParameter('direction', 'received')
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de messages non lus pour une demande de contact
     *
     * @param ContactRequest $contactRequest
     * @return int
     */
    public function countUnreadByContactRequest(ContactRequest $contactRequest): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.contactRequest = :contactRequest')
            ->andWhere('m.isRead = false')
            ->andWhere('m.direction = :direction')
            ->setParameter('contactRequest', $contactRequest)
            ->setParameter('direction', 'received')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche un message par son Message-ID
     *
     * @param string $messageId
     * @return ConversationMessage|null
     */
    public function findByMessageId(string $messageId): ?ConversationMessage
    {
        return $this->createQueryBuilder('m')
            ->where('m.messageId = :messageId')
            ->setParameter('messageId', $messageId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Marque tous les messages reçus d'une demande comme lus
     *
     * @param ContactRequest $contactRequest
     * @return int Nombre de messages marqués comme lus
     */
    public function markAllAsReadForContactRequest(ContactRequest $contactRequest): int
    {
        return $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', true)
            ->where('m.contactRequest = :contactRequest')
            ->andWhere('m.direction = :direction')
            ->andWhere('m.isRead = false')
            ->setParameter('contactRequest', $contactRequest)
            ->setParameter('direction', 'received')
            ->getQuery()
            ->execute();
    }

    /**
     * Récupère le dernier message d'une conversation
     *
     * @param ContactRequest $contactRequest
     * @return ConversationMessage|null
     */
    public function findLastMessage(ContactRequest $contactRequest): ?ConversationMessage
    {
        return $this->createQueryBuilder('m')
            ->where('m.contactRequest = :contactRequest')
            ->setParameter('contactRequest', $contactRequest)
            ->orderBy('m.sentAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Listener pour les événements de sécurité
 *
 * Enregistre les tentatives de connexion (succès et échecs)
 * pour détecter les attaques potentielles
 */
#[AsEventListener(event: LoginSuccessEvent::class)]
#[AsEventListener(event: LoginFailureEvent::class)]
class SecurityEventListener
{
    public function __construct(
        private LoggerInterface $logger,
        private RequestStack $requestStack,
    ) {}

    public function __invoke(LoginSuccessEvent|LoginFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ipAddress = $request?->getClientIp() ?? 'unknown';
        $userAgent = $request?->headers->get('User-Agent') ?? 'unknown';

        if ($event instanceof LoginSuccessEvent) {
            $user = $event->getUser();
            $username = $user->getUserIdentifier();

            $this->logger->info('Connexion réussie', [
                'username' => $username,
                'ip' => $ipAddress,
                'user_agent' => $userAgent,
                'timestamp' => new \DateTime(),
            ]);
        } elseif ($event instanceof LoginFailureEvent) {
            $exception = $event->getException();
            $passport = $event->getPassport();

            // Récupérer l'identifiant de l'utilisateur depuis le passport
            $username = 'unknown';
            if ($passport && method_exists($passport, 'getUser')) {
                try {
                    $user = $passport->getUser();
                    $username = $user?->getUserIdentifier() ?? 'unknown';
                } catch (\Exception $e) {
                    // Si on ne peut pas récupérer l'utilisateur, on garde 'unknown'
                }
            }

            $this->logger->warning('Tentative de connexion échouée', [
                'username' => $username,
                'ip' => $ipAddress,
                'user_agent' => $userAgent,
                'reason' => $exception->getMessage(),
                'timestamp' => new \DateTime(),
            ]);
        }
    }
}

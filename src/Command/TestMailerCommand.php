<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Commande de test pour vérifier la configuration email IONOS
 */
#[AsCommand(
    name: 'app:test-mailer',
    description: 'Teste l\'envoi d\'email via la configuration IONOS',
)]
class TestMailerCommand extends Command
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('to', InputArgument::REQUIRED, 'Adresse email de destination pour le test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $toEmail = $input->getArgument('to');

        $io->title('Test de configuration email IONOS');
        $io->section('Paramètres du test');

        $io->text([
            'Expéditeur : contact@digitalfy.fr',
            'Destinataire : ' . $toEmail,
            'Serveur SMTP : Configuré dans MAILER_DSN',
        ]);

        $io->section('Envoi du mail de test...');

        try {
            // Création d'un email simple pour le test
            $email = (new Email())
                ->from('contact@digitalfy.fr')
                ->to($toEmail)
                ->subject('Test de configuration IONOS - ' . date('H:i:s'))
                ->text('Ceci est un email de test envoyé depuis Digitalfy.')
                ->html('<p>Ceci est un <strong>email de test</strong> envoyé depuis Digitalfy.</p>
                        <p>Date/Heure : ' . date('d/m/Y H:i:s') . '</p>
                        <p>Si vous recevez ce message, votre configuration IONOS fonctionne correctement.</p>');

            // Tentative d'envoi
            $this->mailer->send($email);

            $io->success([
                'Email envoyé avec succès !',
                'Vérifiez la boîte de réception de : ' . $toEmail,
                'Pensez aussi à vérifier vos SPAMS/courrier indésirable.',
            ]);

            $io->note([
                'Si vous ne recevez pas l\'email :',
                '1. Vérifiez vos spams/courrier indésirable',
                '2. Vérifiez votre MAILER_DSN dans .env.local',
                '3. Vérifiez que le serveur SMTP est bien smtp.ionos.fr',
                '4. Vérifiez que le port est 587 (STARTTLS) ou 465 (SSL)',
                '5. Vérifiez que vos identifiants IONOS sont corrects',
                '6. Consultez les logs : var/log/dev.log',
            ]);

            return Command::SUCCESS;

        } catch (TransportExceptionInterface $e) {
            $io->error([
                'Erreur lors de l\'envoi de l\'email !',
                'Message d\'erreur : ' . $e->getMessage(),
            ]);

            $io->section('Solutions possibles');
            $io->listing([
                'Vérifiez votre MAILER_DSN dans .env.local',
                'Format correct : smtp://contact@digitalfy.fr:MOT_DE_PASSE@smtp.ionos.fr:587',
                'Encodez les caractères spéciaux dans le mot de passe (# = %23)',
                'Vérifiez que le compte email existe dans votre espace IONOS',
                'Vérifiez que le mot de passe est correct',
                'Essayez le port 465 avec smtps:// au lieu de smtp://',
            ]);

            return Command::FAILURE;
        }
    }
}

<?php

namespace App\Command;

use App\Service\EmailImapFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Commande pour récupérer les emails via IMAP
 *
 * Cette commande se connecte à la boîte email IONOS,
 * récupère les nouveaux emails reçus, et les associe
 * automatiquement aux demandes de contact.
 *
 * Usage:
 * - Manuel : php bin/console app:fetch-emails
 * - CRON : * php /path/to/project/bin/console app:fetch-emails --quiet
 */
#[AsCommand(
    name: 'app:fetch-emails',
    description: 'Récupère les nouveaux emails de la boîte IONOS et les associe aux demandes de contact',
)]
class FetchEmailsCommand extends Command
{
    public function __construct(
        private readonly EmailImapFetcher $emailImapFetcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Affiche les emails sans les traiter'
            )
            ->setHelp(
                <<<'HELP'
Cette commande récupère automatiquement les nouveaux emails de votre boîte IONOS
et les associe aux demandes de contact correspondantes.

<info>Utilisation :</info>

  Récupération manuelle :
  <comment>php bin/console app:fetch-emails</comment>

  Mode simulation (dry-run) :
  <comment>php bin/console app:fetch-emails --dry-run</comment>

  Via CRON (toutes les 5 minutes) :
  <comment>*/5 * * * * cd /path/to/project && php bin/console app:fetch-emails --quiet</comment>

<info>Prérequis :</info>

  1. Extension PHP IMAP installée : apt-get install php-imap
  2. Configuration IMAP dans .env.local :
     IMAP_HOST=imap.ionos.fr
     IMAP_USERNAME=contact@digitalfy.fr
     IMAP_PASSWORD=votre_mot_de_passe
     IMAP_PORT=993
     IMAP_SSL=true

HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $isDryRun = $input->getOption('dry-run');

        $io->title('Récupération des emails IONOS via IMAP');

        if ($isDryRun) {
            $io->warning('Mode DRY-RUN : Les emails ne seront pas traités');
        }

        try {
            $io->section('Connexion IMAP et récupération des emails...');

            if ($isDryRun) {
                $io->note('Mode simulation activé - aucun email ne sera traité');
                return Command::SUCCESS;
            }

            // Récupération des emails
            $count = $this->emailImapFetcher->fetchNewEmails();

            if ($count === 0) {
                $io->info('Aucun nouvel email à traiter');
                return Command::SUCCESS;
            }

            $io->success(sprintf(
                '%d email(s) récupéré(s) et traité(s) avec succès !',
                $count
            ));

            $io->note([
                'Les messages ont été associés aux demandes de contact correspondantes.',
                'Vous pouvez les consulter dans le back-office EasyAdmin.',
            ]);

            return Command::SUCCESS;

        } catch (\RuntimeException $e) {
            $io->error([
                'Erreur lors de la récupération des emails',
                $e->getMessage(),
            ]);

            // Affichage de l'aide pour les erreurs courantes
            if (str_contains($e->getMessage(), 'IMAP n\'est pas installée')) {
                $io->section('Solution');
                $io->listing([
                    'Installez l\'extension PHP IMAP :',
                    '  Ubuntu/Debian : sudo apt-get install php-imap',
                    '  CentOS/RHEL : sudo yum install php-imap',
                    '  macOS : brew install php-imap',
                    '',
                    'Puis redémarrez PHP-FPM ou Apache',
                ]);
            } elseif (str_contains($e->getMessage(), 'se connecter à la boîte IMAP')) {
                $io->section('Solution');
                $io->listing([
                    'Vérifiez votre configuration IMAP dans .env.local :',
                    '  IMAP_HOST=imap.ionos.fr',
                    '  IMAP_USERNAME=contact@digitalfy.fr',
                    '  IMAP_PASSWORD=votre_mot_de_passe',
                    '  IMAP_PORT=993',
                    '  IMAP_SSL=true',
                    '',
                    'Vérifiez que l\'accès IMAP est activé dans votre espace IONOS',
                ]);
            }

            return Command::FAILURE;

        } catch (\Exception $e) {
            $io->error([
                'Une erreur inattendue s\'est produite',
                $e->getMessage(),
            ]);

            if ($output->isVerbose()) {
                $io->section('Stack trace');
                $io->text($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}

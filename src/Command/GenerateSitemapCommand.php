<?php

namespace App\Command;

use App\Service\SitemapChangeTracker;
use App\Service\SitemapGeneratorService;
use App\Service\SearchEnginePingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sitemap:generate',
    description: 'Génère le fichier sitemap.xml statique',
)]
class GenerateSitemapCommand extends Command
{
    public function __construct(
        private readonly SitemapGeneratorService $sitemapGenerator,
        private readonly SearchEnginePingService $pingService,
        private readonly SitemapChangeTracker $changeTracker,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force la régénération même sans flag de changement'
            )
            ->addOption(
                'ping',
                'p',
                InputOption::VALUE_NONE,
                'Ping les moteurs de recherche après génération'
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Affiche les URLs sans générer le fichier'
            )
            ->setHelp(<<<'HELP'
Cette commande génère le fichier sitemap.xml dans le répertoire public/.

Par défaut, elle vérifie d'abord si un flag de changement existe pour éviter
les régénérations inutiles. Utilise --force pour ignorer le flag.

<info>Exemples d'utilisation :</info>

  # Génération standard (uniquement si flag présent)
  <comment>php bin/console app:sitemap:generate</comment>

  # Force la génération et ping les moteurs
  <comment>php bin/console app:sitemap:generate --force --ping</comment>

  # Prévisualisation sans écrire le fichier
  <comment>php bin/console app:sitemap:generate --dry-run</comment>

<info>Configuration CRON recommandée :</info>

  # Toutes les 15 minutes (uniquement si changements)
  <comment>*/15 * * * * php bin/console app:sitemap:generate --ping --quiet</comment>

  # Toutes les 30 minutes en forçant
  <comment>*/30 * * * * php bin/console app:sitemap:generate --force --ping --quiet</comment>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $startTime = microtime(true);

        $force = $input->getOption('force');
        $ping = $input->getOption('ping');
        $dryRun = $input->getOption('dry-run');

        // Titre
        if (!$output->isQuiet()) {
            $io->title('Génération du sitemap');
        }

        // Vérifier le flag de changement (sauf si --force)
        if (!$force && !$dryRun && !$this->changeTracker->needsUpdate()) {
            if (!$output->isQuiet()) {
                $io->info('Aucun changement détecté depuis la dernière génération.');
                $io->note('Utilisez --force pour régénérer malgré tout.');
            }
            return Command::SUCCESS;
        }

        // Mode dry-run : afficher les URLs sans générer
        if ($dryRun) {
            $urls = $this->sitemapGenerator->getSitemapUrls();
            $io->section(sprintf('Prévisualisation : %d URLs', count($urls)));

            foreach ($urls as $index => $urlData) {
                $io->writeln(sprintf(
                    '[%d] %s (priorité: %.1f, fréquence: %s)',
                    $index + 1,
                    $urlData['loc'],
                    $urlData['priority'],
                    $urlData['changefreq']
                ));
            }

            $io->success('Prévisualisation terminée.');
            return Command::SUCCESS;
        }

        // Génération du sitemap
        if (!$output->isQuiet()) {
            $io->text('Génération du sitemap en cours...');
        }

        $result = $this->sitemapGenerator->generateStaticSitemap();

        // Traiter le résultat
        if (!$result['success']) {
            $io->error($result['message']);
            return Command::FAILURE;
        }

        // Supprimer le flag de changement
        $this->changeTracker->markAsUpdated();

        // Temps d'exécution
        $duration = round((microtime(true) - $startTime) * 1000);

        if (!$output->isQuiet()) {
            $io->success($result['message']);
            $io->info(sprintf('Fichier : %s', $this->sitemapGenerator->getSitemapPath()));
            $io->info(sprintf('Temps d\'exécution : %d ms', $duration));
        }

        // Ping des moteurs de recherche si demandé
        if ($ping) {
            if (!$output->isQuiet()) {
                $io->section('Notification des moteurs de recherche');
            }

            $pingResult = $this->pingService->pingSearchEngines();

            if (!$output->isQuiet()) {
                if ($pingResult['success']) {
                    $io->success('Moteurs de recherche notifiés avec succès');

                    foreach ($pingResult['results'] as $engine => $engineResult) {
                        $status = $engineResult['success'] ? '✓' : '✗';
                        $io->text(sprintf(
                            '%s %s : %s',
                            $status,
                            ucfirst($engine),
                            $engineResult['message']
                        ));
                    }
                } else {
                    $io->warning('Échec de la notification des moteurs de recherche');
                    $io->text($pingResult['message']);
                }
            }
        }

        return Command::SUCCESS;
    }
}

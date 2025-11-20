<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\DBAL\Connection;

#[AsCommand(
    name: 'app:dump-env',
    description: 'Affiche l\'environnement actuel et la configuration de la base de données',
)]
class DumpEnvCommand extends Command
{
    public function __construct(
        private Connection $connection,
        private ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Configuration de l\'environnement Symfony');

        // Environnement
        $io->section('Environnement');
        $io->table(
            ['Variable', 'Valeur'],
            [
                ['APP_ENV', $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'non défini'],
                ['APP_DEBUG', $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? 'non défini'],
                ['Kernel Environment', $this->connection->getParams()['host'] ?? 'N/A'],
            ]
        );

        // Base de données
        $io->section('Configuration Base de Données');

        try {
            $params = $this->connection->getParams();

            $io->table(
                ['Paramètre', 'Valeur'],
                [
                    ['Driver', $params['driver'] ?? 'N/A'],
                    ['Host', $params['host'] ?? 'N/A'],
                    ['Port', $params['port'] ?? 'N/A'],
                    ['Database', $params['dbname'] ?? 'N/A'],
                    ['User', $params['user'] ?? 'N/A'],
                    ['Password', $params['password'] ? '***' : 'vide'],
                ]
            );

            // Test de connexion
            $io->section('Test de Connexion');
            $this->connection->connect();

            $version = $this->connection->fetchOne('SELECT VERSION()');

            $io->success('✓ Connexion à la base de données réussie !');
            $io->writeln("Version MySQL/MariaDB : <info>$version</info>");

            // Liste des bases de données
            $databases = $this->connection->fetchAllAssociative(
                "SHOW DATABASES LIKE 'digitalfy%'"
            );

            if (!empty($databases)) {
                $io->section('Bases de données digitalfy');
                foreach ($databases as $db) {
                    $dbName = reset($db);
                    $io->writeln("  - <comment>$dbName</comment>");
                }
            }

        } catch (\Exception $e) {
            $io->error('✗ Erreur de connexion à la base de données');
            $io->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Fichiers .env chargés
        $io->section('Fichiers .env chargés');
        $projectDir = $this->params->get('kernel.project_dir');
        $envFiles = [
            '.env',
            '.env.local',
            '.env.' . ($_ENV['APP_ENV'] ?? 'dev'),
            '.env.' . ($_ENV['APP_ENV'] ?? 'dev') . '.local',
        ];

        foreach ($envFiles as $file) {
            $path = $projectDir . '/' . $file;
            if (file_exists($path)) {
                $io->writeln("  ✓ <info>$file</info> (existe)");
            } else {
                $io->writeln("  ✗ <comment>$file</comment> (n'existe pas)");
            }
        }

        $io->success('Configuration affichée avec succès !');

        return Command::SUCCESS;
    }
}

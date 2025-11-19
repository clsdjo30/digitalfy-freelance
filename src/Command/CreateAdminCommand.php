<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un nouvel utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Email de l\'administrateur')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, 'Mot de passe')
            ->addOption('fullname', null, InputOption::VALUE_OPTIONAL, 'Nom complet')
            ->setHelp('Cette commande permet de créer un utilisateur administrateur avec le rôle ROLE_ADMIN');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création d\'un administrateur');

        // Récupérer ou demander l'email
        $email = $input->getOption('email');
        if (!$email) {
            $question = new Question('Email de l\'administrateur: ');
            $question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('L\'email ne peut pas être vide');
                }
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Email invalide');
                }
                return $value;
            });
            $email = $io->askQuestion($question);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà.');
            return Command::FAILURE;
        }

        // Récupérer ou demander le nom complet
        $fullName = $input->getOption('fullname');
        if (!$fullName) {
            $question = new Question('Nom complet: ');
            $question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('Le nom ne peut pas être vide');
                }
                return $value;
            });
            $fullName = $io->askQuestion($question);
        }

        // Récupérer ou demander le mot de passe
        $password = $input->getOption('password');
        if (!$password) {
            $question = new Question('Mot de passe (min. 8 caractères): ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('Le mot de passe ne peut pas être vide');
                }
                if (strlen($value) < 8) {
                    throw new \Exception('Le mot de passe doit contenir au moins 8 caractères');
                }
                return $value;
            });
            $password = $io->askQuestion($question);

            $question = new Question('Confirmer le mot de passe: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $confirmPassword = $io->askQuestion($question);

            if ($password !== $confirmPassword) {
                $io->error('Les mots de passe ne correspondent pas.');
                return Command::FAILURE;
            }
        }

        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsActive(true);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Valider l'utilisateur
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        // Persister l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success([
            'Administrateur créé avec succès !',
            sprintf('Email: %s', $email),
            sprintf('Nom: %s', $fullName),
            'Rôle: ROLE_ADMIN',
        ]);

        $io->note('Vous pouvez maintenant vous connecter à /login avec ces identifiants.');

        return Command::SUCCESS;
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();

        $projects = [
            [
                'title' => 'Application mobile de gestion de budget',
                'slug' => 'app-budget-react-native',
                'description' => 'Application mobile de gestion de budget développée avec React Native/Expo pour aider les utilisateurs à mieux suivre leurs dépenses et tenir leurs objectifs financiers.',
                'technologies' => ['React Native', 'Expo', 'TypeScript', 'Firebase'],
                'context' => $this->getAppBudgetContext(),
                'solution' => $this->getAppBudgetSolution(),
                'results' => $this->getAppBudgetResults(),
                'published' => true,
            ],
            [
                'title' => 'Site vitrine pour domaine viticole',
                'slug' => 'site-vigneron-symfony',
                'description' => 'Site vitrine élégant pour un domaine viticole, développé avec Symfony. Présentation des cuvées, de l\'histoire du domaine et optimisation SEO local.',
                'technologies' => ['Symfony', 'Twig', 'Doctrine', 'SEO'],
                'context' => $this->getSiteVigneronContext(),
                'solution' => $this->getSiteVigneronSolution(),
                'results' => $this->getSiteVigneronResults(),
                'published' => true,
            ],
            [
                'title' => 'Site web pour groupe de rock',
                'slug' => 'site-groupe-rock-react',
                'description' => 'Site web dynamique pour un groupe de rock, développé avec React. Dates de concerts, actualités, galerie photos et intégration des playlists.',
                'technologies' => ['React', 'Tailwind CSS', 'Vite'],
                'context' => $this->getSiteRockContext(),
                'solution' => $this->getSiteRockSolution(),
                'results' => $this->getSiteRockResults(),
                'published' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = new Project();
            $project->setTitle($projectData['title']);
            $project->setSlug($projectData['slug']);
            $project->setDescription($projectData['description']);
            $project->setTechnologies($projectData['technologies']);
            $project->setContext($projectData['context']);
            $project->setSolution($projectData['solution']);
            $project->setResults($projectData['results']);
            $project->setPublished($projectData['published']);
            $project->setCreatedAt(new \DateTime('-' . rand(30, 180) . ' days'));

            $manager->persist($project);
        }

        $manager->flush();
    }

    private function getAppBudgetContext(): string
    {
        return <<<HTML
<p>Cette application mobile de gestion de budget a été conçue pour aider les utilisateurs à mieux suivre leurs dépenses, structurer leurs catégories de budget et tenir leurs objectifs financiers sans se perdre dans des tableaux Excel complexes.</p>
<p>Les objectifs principaux étaient :</p>
<ul>
    <li>Suivi simple des dépenses au quotidien</li>
    <li>Vision claire des montants restants dans chaque catégorie</li>
    <li>Utilisation agréable sur mobile, même pour des profils non techniques</li>
</ul>
HTML;
    }

    private function getAppBudgetSolution(): string
    {
        return <<<HTML
<p>J'ai conçu une application mobile multiplateforme (iOS et Android) avec React Native/Expo. L'interface est volontairement épurée, avec des écrans clairs et un parcours utilisateur simple : ajouter une dépense, visualiser ses enveloppes, consulter son historique.</p>

<h3>Fonctionnalités principales</h3>
<ul>
    <li>Création d'enveloppes de budget (logement, courses, sorties, etc.)</li>
    <li>Ajout rapide de dépenses dans chaque enveloppe</li>
    <li>Suivi du budget restant par enveloppe et par mois</li>
    <li>Vue globale sur le budget mensuel et annuel</li>
    <li>Interface mobile optimisée pour une utilisation d'une seule main</li>
</ul>

<h3>Stack technique</h3>
<ul>
    <li><strong>Front mobile</strong> : React Native + Expo</li>
    <li><strong>Langage</strong> : TypeScript pour plus de robustesse</li>
    <li><strong>Backend / stockage</strong> : Firebase</li>
    <li><strong>Outils</strong> : ESLint/Prettier pour la qualité du code</li>
</ul>
HTML;
    }

    private function getAppBudgetResults(): string
    {
        return <<<HTML
<p>L'application montre comment un outil pensé pour l'utilisateur peut transformer une tâche perçue comme pénible (gérer son budget) en un automatisme simple.</p>
<p>Ce type de projet peut être adapté pour :</p>
<ul>
    <li>Des outils internes en entreprise</li>
    <li>Des applications de coaching ou d'accompagnement</li>
    <li>Des solutions marque blanche pour des conseillers financiers</li>
</ul>
HTML;
    }

    private function getSiteVigneronContext(): string
    {
        return <<<HTML
<p>Le projet vise un domaine viticole souhaitant présenter ses cuvées, son histoire et accueillir plus de visiteurs. Le domaine avait besoin d'un site simple, élégant et optimisé pour apparaître dans les résultats de recherche locaux.</p>

<h3>Objectifs du site</h3>
<ul>
    <li>Valoriser l'image du domaine</li>
    <li>Présenter les cuvées de manière claire et attractive</li>
    <li>Faciliter la prise de contact et les visites au caveau</li>
    <li>Poser les bases d'une future vente en ligne</li>
</ul>
HTML;
    }

    private function getSiteVigneronSolution(): string
    {
        return <<<HTML
<h3>Structure des pages</h3>
<ul>
    <li>Accueil avec mise en avant des cuvées et de l'histoire du domaine</li>
    <li>Page "Le domaine" : histoire, terroir, philosophie</li>
    <li>Page "Les vins" : fiches par cuvée</li>
    <li>Page "Visites & contact" : horaires, plan d'accès, formulaire</li>
    <li>Mentions légales et conformité</li>
</ul>

<h3>Stack technique</h3>
<ul>
    <li><strong>Backend</strong> : Symfony</li>
    <li><strong>Frontend</strong> : Twig pour un rendu serveur optimisé SEO</li>
    <li><strong>Formulaire de contact</strong> avec protection anti-spam</li>
    <li><strong>Optimisation SEO</strong> : balises title/meta, structure Hn, URL propres, sitemap</li>
</ul>
HTML;
    }

    private function getSiteVigneronResults(): string
    {
        return <<<HTML
<p>Grâce à ce site vitrine, le domaine dispose d'une présence en ligne professionnelle, qui renvoie une image cohérente avec la qualité de son travail. Les visiteurs trouvent facilement les informations essentielles : localisation, horaires, cuvées, contact.</p>
<p>Le site constitue une base solide pour de futures évolutions, comme l'ajout d'un système de vente en ligne ou d'un espace de réservation pour les visites.</p>
HTML;
    }

    private function getSiteRockContext(): string
    {
        return <<<HTML
<p>Un groupe de rock a besoin d'un site pour regrouper ses informations : dates de concerts, actualités, photos, vidéos et liens vers les réseaux sociaux. L'objectif est de proposer un point central pour les fans, les organisateurs et les salles.</p>
HTML;
    }

    private function getSiteRockSolution(): string
    {
        return <<<HTML
<h3>Fonctionnalités du site</h3>
<ul>
    <li>Page d'accueil avec mise en avant du prochain concert</li>
    <li>Page "Concerts" listant les dates passées et à venir</li>
    <li>Page "Actualités" pour les annonces importantes</li>
    <li>Galerie photos</li>
    <li>Intégration de playlists (Spotify, YouTube, etc.)</li>
    <li>Formulaire de contact pour les organisateurs</li>
</ul>

<h3>Stack technique</h3>
<ul>
    <li><strong>Frontend</strong> : React avec Vite</li>
    <li><strong>CSS</strong> : Tailwind CSS pour un design moderne et responsive</li>
    <li><strong>Intégrations</strong> : liens vers réseaux sociaux et plateformes de streaming</li>
    <li><strong>SEO</strong> : titres et contenus optimisés sur le nom du groupe et les mots-clés "rock / concerts"</li>
</ul>
HTML;
    }

    private function getSiteRockResults(): string
    {
        return <<<HTML
<p>Le groupe dispose d'un site professionnel pour communiquer et centraliser ses informations. Il peut partager un seul lien dans ses communications, et les fans trouvent facilement les dates et les contenus.</p>
<p>Le site renforce la crédibilité du groupe auprès des organisateurs de concerts et facilite les prises de contact pour de nouvelles dates.</p>
HTML;
    }
}

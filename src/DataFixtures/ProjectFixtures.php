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
                'title' => 'Application Mobile Restaurant La Table du 2',
                'description' => 'Application mobile complète pour restaurant gastronomique avec système de réservation et click & collect.',
                'technologies' => ['React Native', 'Expo', 'Symfony API', 'Stripe'],
                'context' => 'Restaurant gastronomique souhaitant digitaliser son service et proposer un système de réservation moderne.',
                'solution' => 'Développement d\'une application mobile cross-platform avec React Native permettant les réservations, la consultation du menu, et les commandes à emporter.',
                'results' => '+35% de réservations en ligne, -60% de temps de gestion administrative',
                'published' => true,
            ],
            [
                'title' => 'Site E-commerce Boutique Bio Nature',
                'description' => 'Boutique en ligne pour produits biologiques locaux avec système de livraison géolocalisé.',
                'technologies' => ['Symfony', 'Sylius', 'Stripe', 'API Google Maps'],
                'context' => 'Commerce de proximité souhaitant développer ses ventes en ligne tout en conservant son identité locale.',
                'solution' => 'Création d\'un site e-commerce Symfony avec Sylius, intégration de zones de livraison géolocalisées et mise en avant des producteurs locaux.',
                'results' => '+120% CA en 6 mois, 450+ clients actifs',
                'published' => true,
            ],
            [
                'title' => 'Plateforme SaaS Gestion Événementiel',
                'description' => 'Solution SaaS pour agences événementielles : gestion clients, devis, facturation, planning.',
                'technologies' => ['Symfony', 'API Platform', 'Vue.js', 'PostgreSQL'],
                'context' => 'Agence événementielle cherchant à optimiser sa gestion administrative et son suivi client.',
                'solution' => 'Développement d\'une plateforme SaaS complète avec CRM, gestion de devis/factures, planning collaboratif et tableau de bord analytique.',
                'results' => '-70% temps administratif, +25% efficacité équipe',
                'published' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = new Project();
            $project->setTitle($projectData['title']);
            $project->setSlug($slugify->slugify($projectData['title']));
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
}

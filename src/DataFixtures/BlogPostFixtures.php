<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;

class BlogPostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();

        $posts = [
            [
                'title' => 'Pourquoi choisir Symfony pour votre projet web à Nîmes',
                'excerpt' => 'Symfony est le framework PHP de référence pour les projets web professionnels. Découvrez pourquoi c\'est le bon choix pour votre entreprise.',
                'content' => '<h2>Introduction</h2><p>Symfony est un framework PHP mature et robuste...</p>',
                'category' => 'developpement-web',
                'status' => 'published',
                'metaTitle' => 'Développeur Symfony Nîmes - Expert Freelance',
                'metaDescription' => 'Développeur Symfony freelance à Nîmes. Création de sites web professionnels et applications métier sur mesure.',
            ],
            [
                'title' => 'Application mobile React Native pour restaurants à Nîmes',
                'excerpt' => 'Comment une application mobile peut transformer votre restaurant et améliorer l\'expérience client.',
                'content' => '<h2>Les avantages d\'une app mobile</h2><p>Une application mobile permet...</p>',
                'category' => 'applications-mobiles',
                'status' => 'published',
                'metaTitle' => 'Application Mobile Restaurant Nîmes - React Native',
                'metaDescription' => 'Développement d\'applications mobiles pour restaurants à Nîmes. Click & Collect, réservations, programme fidélité.',
            ],
            [
                'title' => 'SEO Local à Nîmes : 10 conseils pour les TPE/PME',
                'excerpt' => 'Optimisez votre visibilité locale sur Google avec ces conseils pratiques adaptés aux petites entreprises.',
                'content' => '<h2>L\'importance du SEO local</h2><p>Le référencement local est crucial...</p>',
                'category' => 'seo-local',
                'status' => 'published',
                'metaTitle' => 'SEO Local Nîmes - 10 Conseils pour TPE/PME',
                'metaDescription' => 'Guide complet du référencement local à Nîmes. Conseils pratiques pour améliorer votre visibilité sur Google.',
            ],
        ];

        foreach ($posts as $postData) {
            $post = new BlogPost();
            $post->setTitle($postData['title']);
            $post->setSlug($slugify->slugify($postData['title']));
            $post->setExcerpt($postData['excerpt']);
            $post->setContent($postData['content']);
            $post->setCategory($this->getReference('category-' . $postData['category']));
            $post->setStatus($postData['status']);
            $post->setMetaTitle($postData['metaTitle']);
            $post->setMetaDescription($postData['metaDescription']);
            $post->setPublishedAt(new \DateTime('-' . rand(1, 30) . ' days'));
            $post->setCreatedAt(new \DateTime('-' . rand(1, 30) . ' days'));
            $post->setUpdatedAt(new \DateTime());

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

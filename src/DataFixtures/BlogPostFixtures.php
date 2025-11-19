<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Category;
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
                'title' => 'Click & Collect à Nîmes : pourquoi votre restaurant doit s\'y mettre',
                'slug' => 'click-and-collect-nimes',
                'excerpt' => 'Le Click & Collect s\'est imposé comme une solution simple et rentable pour les restaurants. À Nîmes, de plus en plus de clients souhaitent commander en ligne puis récupérer leur commande sur place, sans attendre.',
                'content' => $this->getClickCollectContent(),
                'category' => 'solutions-digitales',
                'status' => 'published',
                'metaTitle' => 'Click & Collect à Nîmes : pourquoi votre restaurant doit s\'y mettre | Digitalfy',
                'metaDescription' => 'Les avantages du Click & Collect pour les restaurants de Nîmes : gains de temps, augmentation du chiffre d\'affaires, meilleure organisation.',
            ],
            [
                'title' => 'Application mobile pour restaurant : quels avantages concrets ?',
                'slug' => 'application-mobile-pour-restaurant',
                'excerpt' => 'Les grandes chaînes ont leurs applications depuis longtemps, mais une app mobile peut aussi être très utile pour un restaurant indépendant. À quoi peut-elle vous servir concrètement ?',
                'content' => $this->getAppRestaurantContent(),
                'category' => 'applications-mobiles',
                'status' => 'published',
                'metaTitle' => 'Application mobile pour restaurant : quels avantages concrets ? | Digitalfy',
                'metaDescription' => 'Quels sont les avantages d\'une application mobile pour un restaurant ? Fidélisation, commandes en ligne, communication directe et meilleure expérience client.',
            ],
            [
                'title' => 'Pourquoi chaque artisan à Nîmes a besoin d\'un site internet',
                'slug' => 'site-internet-pour-artisan',
                'excerpt' => 'Boulangers, plombiers, électriciens, coiffeurs, mécaniciens, peintres : vos clients vous cherchent de plus en plus sur internet avant de vous appeler. Si vous n\'avez pas de site, ils risquent de ne jamais vous trouver.',
                'content' => $this->getSiteArtisanContent(),
                'category' => 'developpement-web',
                'status' => 'published',
                'metaTitle' => 'Pourquoi chaque artisan à Nîmes a besoin d\'un site internet | Digitalfy',
                'metaDescription' => 'Un site internet permet à un artisan de Nîmes d\'être visible, de rassurer ses clients et de générer des demandes de devis. Explications et conseils pratiques.',
            ],
        ];

        foreach ($posts as $postData) {
            $post = new BlogPost();
            $post->setTitle($postData['title']);
            $post->setSlug($postData['slug']);
            $post->setExcerpt($postData['excerpt']);
            $post->setContent($postData['content']);
            $post->setCategory($this->getReference('category-' . $postData['category'], Category::class));
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

    private function getClickCollectContent(): string
    {
        return <<<HTML
<h2>Qu'est-ce que le Click & Collect ?</h2>
<p>Le Click & Collect permet à vos clients de :</p>
<ul>
    <li>Commander en ligne depuis un site ou une application</li>
    <li>Choisir un créneau de retrait</li>
    <li>Payer à l'avance ou sur place</li>
    <li>Récupérer leur commande sans faire la queue</li>
</ul>
<p>C'est un parcours client fluide, qui simplifie aussi votre organisation.</p>

<h2>Pourquoi c'est intéressant pour un restaurant à Nîmes ?</h2>
<p>À Nîmes, la concurrence est forte, surtout en centre-ville et dans les zones commerciales. Proposer le Click & Collect permet de :</p>
<ul>
    <li>Attirer une clientèle pressée (pause déjeuner, soir, week-end)</li>
    <li>Se différencier des établissements qui n'ont pas encore digitalisé leurs commandes</li>
    <li>Réduire le temps passé au téléphone à prendre les commandes</li>
</ul>

<h2>Les bénéfices concrets pour votre organisation</h2>
<ul>
    <li>Moins d'erreurs de commande (tout est écrit noir sur blanc)</li>
    <li>Meilleure planification de la préparation en cuisine</li>
    <li>Réduction des appels téléphoniques pendant les coups de feu</li>
    <li>Possibilité d'analyser les produits les plus demandés</li>
</ul>

<h2>Comment mettre en place une solution Click & Collect ?</h2>
<p>Vous pouvez :</p>
<ul>
    <li>Ajouter un module de commande en ligne à votre site existant</li>
    <li>Créer une page dédiée avec votre carte et un formulaire de commande structuré</li>
    <li>Mettre en place une interface simple en cuisine pour suivre les commandes</li>
</ul>
<p>En fonction de votre taille et de votre budget, il est possible de commencer avec une solution simple et de la faire évoluer ensuite.</p>

<h2>Conclusion</h2>
<p>Le Click & Collect est un investissement raisonnable qui peut rapidement améliorer votre chiffre d'affaires et votre organisation interne.</p>
<p>Vous êtes restaurateur à Nîmes ou dans le Gard et vous souhaitez mettre en place une solution de Click & Collect adaptée à votre établissement ? <strong>Contactez Digitalfy</strong> pour en discuter et définir la solution la plus simple et la plus efficace pour vous.</p>
HTML;
    }

    private function getAppRestaurantContent(): string
    {
        return <<<HTML
<h2>Une application mobile, pour quel type de restaurant ?</h2>
<p>Une application mobile peut être pertinente si :</p>
<ul>
    <li>Vous avez une clientèle régulière</li>
    <li>Vous faites beaucoup de vente à emporter</li>
    <li>Vous proposez des menus du jour ou des offres spéciales</li>
    <li>Vous souhaitez fidéliser vos clients avec un programme simple</li>
</ul>

<h2>Les principaux avantages pour vos clients</h2>
<ul>
    <li>Commander facilement depuis leur téléphone</li>
    <li>Consulter la carte et les menus actualisés</li>
    <li>Être informés des promotions et nouveautés</li>
    <li>Accéder rapidement à vos coordonnées et à votre localisation</li>
</ul>

<h2>Les avantages pour votre organisation</h2>
<ul>
    <li>Réduction des appels pour les commandes</li>
    <li>Centralisation des demandes dans un seul outil</li>
    <li>Possibilité de mieux anticiper les préparations</li>
    <li>Communication directe avec vos clients via des notifications</li>
</ul>

<h2>Exemple de fonctionnalités utiles</h2>
<ul>
    <li>Carte interactive avec filtres (plats, formules, menus du jour)</li>
    <li>Système de commande et de paiement</li>
    <li>Programme de fidélité simple (X commandes = avantage)</li>
    <li>Historique des commandes pour gagner du temps</li>
</ul>

<h2>Comment démarrer un projet d'application ?</h2>
<p>Vous n'avez pas besoin de tout imaginer dès le départ. On peut commencer par un périmètre simple : consultation de la carte + commandes à emporter, par exemple.</p>
<p>Ensemble, nous définissons :</p>
<ul>
    <li>Les objectifs prioritaires</li>
    <li>Les fonctionnalités minimum pour le lancement</li>
    <li>Un budget réaliste et un planning</li>
</ul>

<h2>Conclusion</h2>
<p>Vous êtes restaurateur à Nîmes et vous vous demandez si une application mobile pourrait apporter quelque chose à votre établissement ? Contactez <strong>Digitalfy</strong> pour un échange sans engagement et une première analyse de votre situation.</p>
HTML;
    }

    private function getSiteArtisanContent(): string
    {
        return <<<HTML
<h2>Vos futurs clients vous cherchent en ligne</h2>
<p>Avant de prendre rendez-vous, la plupart des gens :</p>
<ul>
    <li>Taperont votre activité + "Nîmes" dans Google</li>
    <li>Regarderont les premiers résultats</li>
    <li>Cliqueront sur les sites les plus clairs</li>
</ul>
<p>Un site simple, bien structuré, suffit souvent à faire la différence.</p>

<h2>Un site rassure et filtre les demandes</h2>
<p>Un site internet permet de :</p>
<ul>
    <li>Montrer vos réalisations (avant/après, chantiers, photos)</li>
    <li>Expliquer clairement ce que vous faites et ce que vous ne faites pas</li>
    <li>Afficher vos zones d'intervention</li>
    <li>Faciliter la prise de contact (formulaire, téléphone, email)</li>
</ul>
<p><strong>Résultat :</strong> les demandes que vous recevez sont plus qualifiées.</p>

<h2>Ce qu'un site simple peut contenir</h2>
<p>Un bon site d'artisan peut se limiter à quelques pages :</p>
<ul>
    <li>Accueil</li>
    <li>Vos services</li>
    <li>Vos réalisations</li>
    <li>Avis clients (si vous en avez)</li>
    <li>Contact & zone d'intervention</li>
</ul>
<p>L'essentiel est que ces pages soient claires et faciles à lire.</p>

<h2>Les erreurs à éviter</h2>
<ul>
    <li>Site trop chargé ou confus</li>
    <li>Pas de numéro de téléphone visible</li>
    <li>Pas d'adresse ou de zone géographique indiquée</li>
    <li>Photos trop petites ou de mauvaise qualité</li>
    <li>Site non adapté aux smartphones</li>
</ul>

<h2>Par où commencer ?</h2>
<p>Il n'est pas nécessaire de commencer avec un gros site complexe. Un site vitrine simple, bien structuré, est souvent suffisant pour une première étape.</p>
<p>Si vous êtes artisan à Nîmes ou dans les environs, nous pouvons travailler ensemble pour créer un site internet qui reflète votre travail et attire les bons clients.</p>
<p><strong>Contactez Digitalfy</strong> pour discuter de votre projet de site internet.</p>
HTML;
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

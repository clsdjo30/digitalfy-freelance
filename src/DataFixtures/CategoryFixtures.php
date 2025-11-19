<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();

        $categories = [
            'SEO Local' => 'Conseils pour améliorer votre référencement local à Nîmes et alentours',
            'Développement Web' => 'Actualités et astuces sur le développement web avec Symfony',
            'Applications Mobiles' => 'Tout savoir sur les applications mobiles avec React Native et Expo',
            'Solutions Digitales' => 'Solutions pour digitaliser votre entreprise TPE/PME',
        ];

        foreach ($categories as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($slugify->slugify($name));
            $category->setDescription($description);

            $manager->persist($category);

            // Référence pour utiliser dans BlogPostFixtures
            $this->addReference('category-' . $slugify->slugify($name), $category);
        }

        $manager->flush();
    }
}

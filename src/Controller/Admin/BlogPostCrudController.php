<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article de blog')
            ->setEntityLabelInPlural('Articles de blog')
            ->setDefaultSort(['publishedAt' => 'DESC'])
            ->setSearchFields(['title', 'excerpt', 'content']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre')
                ->setHelp('Le titre principal de l\'article'),
            SlugField::new('slug', 'URL')
                ->setTargetFieldName('title')
                ->setHelp('Généré automatiquement depuis le titre'),
            TextareaField::new('excerpt', 'Extrait')
                ->setHelp('Résumé court de l\'article (160 caractères max pour le SEO)')
                ->hideOnIndex(),
            TextareaField::new('content', 'Contenu')
                ->setHelp('Contenu complet de l\'article (HTML autorisé)')
                ->hideOnIndex(),
            AssociationField::new('category', 'Catégorie')
                ->setRequired(true),
            DateTimeField::new('publishedAt', 'Date de publication')
                ->setHelp('Date et heure de publication de l\'article'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => 'draft',
                    'Publié' => 'published',
                ])
                ->renderAsBadges([
                    'draft' => 'warning',
                    'published' => 'success',
                ]),
            TextField::new('metaTitle', 'Meta Title (SEO)')
                ->setHelp('Titre pour les moteurs de recherche (60 caractères max)')
                ->hideOnIndex(),
            TextareaField::new('metaDescription', 'Meta Description (SEO)')
                ->setHelp('Description pour les moteurs de recherche (160 caractères max)')
                ->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
                ->hideOnForm(),
        ];
    }
}

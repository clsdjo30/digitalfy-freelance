<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
                ->setHelp('Le titre principal de l\'article')
                ->setColumns(6),
            SlugField::new('slug', 'URL')
                ->setTargetFieldName('title')
                ->setHelp('Généré automatiquement depuis le titre')
                ->setColumns(6),
            AssociationField::new('category', 'Catégorie')
                ->setRequired(true)
                ->setColumns(6),
            ImageField::new('featuredImage', 'Image actuelle')
                ->setBasePath('/uploads/blog')
                ->setUploadDir('public/uploads/blog')
                ->onlyOnIndex(),
            TextField::new('featuredImageFile', 'Image à la une')
                ->setFormType(VichImageType::class)
                ->setHelp('Image principale de l\'article (JPG, PNG, WebP)')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('imageAlt', 'Texte alternatif image')
                ->setHelp('Description de l\'image pour le SEO et l\'accessibilité (125 caractères max)')
                ->onlyOnForms()
                ->setColumns(6),
            DateTimeField::new('publishedAt', 'Date de publication')
                ->setHelp('Date et heure de publication de l\'article')
                ->setColumns(6),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => 'draft',
                    'Publié' => 'published',
                ])
                ->renderAsBadges([
                    'draft' => 'warning',
                    'published' => 'success',
                ])
                ->setColumns(6),
            TextareaField::new('metaDescription', 'Meta Description (SEO)')
                ->setHelp('Description pour les moteurs de recherche (160 caractères max)')
                ->hideOnIndex()
                ->setColumns(6),
            TextField::new('metaTitle', 'Meta Title (SEO)')
                ->setHelp('Titre pour les moteurs de recherche (60 caractères max)')
                ->hideOnIndex()
                ->setColumns(6),
            TextField::new('focusKeyword', 'Mot-clé principal')
                ->setHelp('Mot-clé SEO principal de l\'article (100 caractères max)')
                ->hideOnIndex()
                ->setColumns(6),
            IntegerField::new('readingTime', 'Temps de lecture')
                ->setHelp('Temps de lecture estimé en minutes (calculé automatiquement si vide)')
                ->hideOnIndex()
                ->setColumns(6),
            TextareaField::new('excerpt', 'Extrait')
                ->setHelp('Résumé court de l\'article (160 caractères max pour le SEO)')
                ->hideOnIndex(),
            TextareaField::new('content', 'Contenu')
                ->setHelp('Contenu de l\'article en Markdown (### Titre, **gras**, *italique*, etc.)')
                ->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
                ->hideOnForm(),
        ];
    }
}

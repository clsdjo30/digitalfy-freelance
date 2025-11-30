<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Galerie d\'images')
            ->setDefaultSort(['uploadedAt' => 'DESC'])
            ->setSearchFields(['title', 'altText', 'filename'])
            ->setPageTitle('index', 'Galerie d\'images')
            ->setPageTitle('new', 'Ajouter une image')
            ->setPageTitle('edit', 'Modifier l\'image')
            ->setPageTitle('detail', 'Détails de l\'image');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('uploadedAt')
            ->add('altText');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->onlyOnIndex(),

            ImageField::new('filename', 'Image actuelle')
                ->setBasePath('/uploads/gallery')
                ->setUploadDir('public/uploads/gallery')
                ->onlyOnIndex(),

            TextField::new('imageFile', 'Télécharger l\'image')
                ->setFormType(VichImageType::class)
                ->setHelp('Formats acceptés : JPG, PNG, WebP, GIF (max 5 Mo)')
                ->onlyOnForms()
                ->setColumns(6),

            TextField::new('title', 'Titre')
                ->setHelp('Titre descriptif de l\'image (optionnel)')
                ->setColumns(6),

            TextField::new('altText', 'Texte alternatif')
                ->setHelp('Description de l\'image pour le SEO et l\'accessibilité (obligatoire)')
                ->setRequired(true)
                ->setColumns(12),

            TextField::new('markdownSyntax', 'Code Markdown')
                ->setHelp('Copiez ce code dans votre article pour afficher l\'image')
                ->hideOnForm()
                ->setColumns(12),

            TextField::new('url', 'URL de l\'image')
                ->setHelp('Chemin de l\'image sur le site')
                ->hideOnForm()
                ->hideOnIndex()
                ->setColumns(12),

            DateTimeField::new('uploadedAt', 'Téléchargée le')
                ->hideOnForm(),

            DateTimeField::new('updatedAt', 'Modifiée le')
                ->hideOnForm()
                ->hideOnIndex(),
        ];
    }
}

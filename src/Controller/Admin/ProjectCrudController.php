<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['title', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre')
                ->setHelp('Titre du projet')
                ->setColumns(6),
            SlugField::new('slug', 'URL')
                ->setTargetFieldName('title')
                ->setHelp('Généré automatiquement depuis le titre')
                ->setColumns(6),
            TextareaField::new('description', 'Description courte')
                ->setHelp('Résumé du projet affiché dans la liste')
                ->setColumns(6),
            ArrayField::new('technologies', 'Technologies')
                ->setHelp('Liste des technologies utilisées (une par ligne)')
                ->setColumns(6),
            ImageField::new('thumbnail', 'Image actuelle')
                ->setBasePath('/uploads/projects')
                ->setUploadDir('public/uploads/projects')
                ->onlyOnIndex(),
            TextField::new('thumbnailFile', 'Image du projet')
                ->setFormType(VichImageType::class)
                ->setHelp('Image principale du projet (JPG, PNG, WebP)')
                ->onlyOnForms()
                ->setColumns(6),
            BooleanField::new('published', 'Publié')
                ->renderAsSwitch(true)
                ->setColumns(6),
            TextareaField::new('context', 'Contexte & objectifs')
                ->setHelp('Contexte et objectifs du projet (HTML autorisé)')
                ->hideOnIndex()
                ->setColumns(6),
            TextareaField::new('solution', 'Solution mise en place')
                ->setHelp('Description de la solution développée (HTML autorisé)')
                ->hideOnIndex()
                ->setColumns(6),
            TextareaField::new('results', 'Résultats & perspectives')
                ->setHelp('Résultats obtenus et perspectives (HTML autorisé)')
                ->hideOnIndex()
                ->setColumns(6),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
        ];
    }
}

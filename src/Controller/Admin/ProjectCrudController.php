<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

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
                ->setHelp('Titre du projet'),
            SlugField::new('slug', 'URL')
                ->setTargetFieldName('title')
                ->setHelp('Généré automatiquement depuis le titre'),
            TextareaField::new('description', 'Description courte')
                ->setHelp('Résumé du projet affiché dans la liste'),
            ArrayField::new('technologies', 'Technologies')
                ->setHelp('Liste des technologies utilisées (une par ligne)'),
            TextareaField::new('context', 'Contexte & objectifs')
                ->setHelp('Contexte et objectifs du projet (HTML autorisé)')
                ->hideOnIndex(),
            TextareaField::new('solution', 'Solution mise en place')
                ->setHelp('Description de la solution développée (HTML autorisé)')
                ->hideOnIndex(),
            TextareaField::new('results', 'Résultats & perspectives')
                ->setHelp('Résultats obtenus et perspectives (HTML autorisé)')
                ->hideOnIndex(),
            BooleanField::new('published', 'Publié')
                ->renderAsSwitch(true),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),
        ];
    }
}

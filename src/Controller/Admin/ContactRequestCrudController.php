<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de contact')
            ->setEntityLabelInPlural('Demandes de contact')
            ->setDefaultSort(['submittedAt' => 'DESC'])
            ->setSearchFields(['name', 'email', 'phone', 'message']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            EmailField::new('email', 'Email'),
            TextField::new('phone', 'Téléphone')
                ->hideOnIndex(),
            TextField::new('projectType', 'Type de projet'),
            TextField::new('estimatedBudget', 'Budget estimé')
                ->hideOnIndex(),
            TextareaField::new('message', 'Message')
                ->hideOnIndex(),
            DateTimeField::new('submittedAt', 'Date de soumission')
                ->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Nouveau' => 'new',
                    'En cours' => 'in_progress',
                    'Clôturé' => 'closed',
                ])
                ->renderAsBadges([
                    'new' => 'info',
                    'in_progress' => 'warning',
                    'closed' => 'success',
                ]),
            TextareaField::new('notes', 'Notes internes')
                ->setHelp('Notes visibles uniquement dans l\'admin')
                ->hideOnIndex(),
        ];
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    // On désactive les actions : créer, modifier, supprimer
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)     // pas de création
            ->disable(Action::EDIT)    // pas d'édition
            ->disable(Action::DELETE)  // pas de suppression
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // garder uniquement "Détails"
    }

    // On configure les champs à afficher
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            DateField::new('startDate', 'Date de début'),
            DateField::new('endDate', 'Date de fin'),

            TextField::new('status', 'Statut'),

            MoneyField::new('totalPrice', 'Total payé')
                ->setCurrency('EUR'),
        ];
    }
}

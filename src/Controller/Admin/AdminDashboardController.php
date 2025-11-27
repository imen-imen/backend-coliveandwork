<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\ColivingCity;
use App\Entity\ColivingSpace;
use App\Entity\PrivateSpace;
use App\Entity\Reservation;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{
    // Route principale de l'administration : /admin
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    // Configuration du titre de l'interface admin
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('COLIVE&WORK - Administration');
    }

    // Menu de gauche : liens vers les CRUD
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil');

        yield MenuItem::linkToCrud('Utilisateurs', null, User::class);
        yield MenuItem::linkToCrud('Villes', null, ColivingCity::class);
        yield MenuItem::linkToCrud('Espaces coliving', null, ColivingSpace::class);
        yield MenuItem::linkToCrud('Espaces privés', null, PrivateSpace::class);
        yield MenuItem::linkToCrud('Réservations', null, Reservation::class);
    }
}

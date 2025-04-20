<?php

namespace App\Controller\Admin;

use App\Entity\EventCollection;
use App\Entity\EventReport;
use App\Entity\Filter;
use App\Entity\News;
use App\Entity\NewsLetter;
use App\Entity\PublicEventStatistic;
use App\Entity\Region;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\PublicEvent;
use App\Entity\Organisation;
use App\Entity\OrganisationCategory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;

#[AdminDashboard(routePath: '/admin/', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $unusedReportCount;
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->unusedReportCount = $entityManager->getRepository(EventReport::class)->count(['used' => false]);

    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Археономика админка')
            ->renderContentMaximized()
            ->renderSidebarMinimized(false)
            ->disableDarkMode(false)
            ->generateRelativeUrls(false);
    }

    public function configureMenuItems(): iterable
    {

        return [
            MenuItem::section('На модерацию')->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('События(' . ($this->unusedReportCount) . ')', 'fa fa-child', EventReport::class)->setPermission('ROLE_SUPER_ADMIN'),

            MenuItem::section('Опубликованные'),
            MenuItem::linkToCrud('События', 'fa fa-child', PublicEvent::class),
            MenuItem::linkToCrud('Категории', 'fa fa-tags', Category::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Теги', 'fa fa-tags', Filter::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Регионы', 'fa fa-map', Region::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Организации', 'fa fa-users', Organisation::class),
            MenuItem::linkToCrud('Типы организаций', 'fa fa-users', OrganisationCategory::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Подборки', 'fa fa-folder-o', EventCollection::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Новости', 'fa fa-folder-o', News::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Рассылки', 'fa fa-folder-o', NewsLetter::class)->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Статистика', 'fa fa-folder-o', PublicEventStatistic::class)->setPermission('ROLE_SUPER_ADMIN'),

            MenuItem::section('Пользователи')->setPermission('ROLE_SUPER_ADMIN'),
            MenuItem::linkToCrud('Пользователи', 'fa fa-user', User::class)->setPermission('ROLE_SUPER_ADMIN'),
        ];
    }
}

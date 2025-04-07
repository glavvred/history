<?php

namespace App\Controller\Admin;

use App\Entity\Organisation;
use App\Entity\PublicEvent;
use App\Entity\PublicEventStatistic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_SUPER_ADMIN')]
class PublicEventStatisticCrudController extends AbstractCrudController
{
    public const ACTION_SAVE_CSV = "CSV";

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public static function getEntityFqcn(): string
    {
        return PublicEventStatistic::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Статистика')
            ->setDefaultSort(['publicEvent' => 'DESC'])
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('managingOrganisation'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('mapClick', 'Карта')->setDisabled(),
            Field::new('organisationClick', 'Организация')->setDisabled(),
            Field::new('buttonClick', 'Кнопка')->setDisabled(),
            AssociationField::new('publicEvent', 'Событие')
                ->setCrudController(PublicEvent::class),
            AssociationField::new('managingOrganisation', 'Организатор')
                ->setCrudController(Organisation::class),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_SAVE_CSV)
            ->linkToCrudAction('saveUsersToCsv')
            ->setCssClass('btn btn-info')
            ->createAsGlobalAction();

        return
            $actions
                ->remove(Crud::PAGE_INDEX, Action::NEW)
                ->remove(Crud::PAGE_INDEX, Action::EDIT)
                ->remove(Crud::PAGE_INDEX, Action::DELETE)
                ->add(Crud::PAGE_INDEX, $duplicate);

//        return parent::configureActions($actions);
    }

    public function saveUsersToCsv(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): Response
    {
        $peRepo = $this->em->getRepository(PublicEventStatistic::class);
        $peRecords = $peRepo->findAll();
        $rows = array();
        $columns = array(
            'map',
            'org',
            'button',
            'public_event_id',
            'managing_organisation_id',
        );
        $rows[] = implode(';', $columns);
        foreach ($peRecords as $peRecord) {
            $data = array(
                $peRecord->getMapClick() ?: 0,
                $peRecord->getOrganisationClick() ?: 0,
                $peRecord->getButtonClick() ?: 0,
                $peRecord->getPublicEvent()->getName(),
                $peRecord->getManagingOrganisation() ? $peRecord->getManagingOrganisation()->getName() : '----',
            );
            $rows[] = implode(';', $data);
        }
        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set("Content-Type", 'text/csv');
        $response->headers->set("Content-Disposition", 'attachment; filename="event_statistics.csv"');

        return $response;
    }
}

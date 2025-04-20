<?php

namespace App\Controller\Admin;

use App\Entity\EventReport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
#[Route('/admin/report', name: 'admin_report')]
class EventReportCrudController extends AbstractCrudController
{
    private $adminUrlGenerator;
    private $request;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, RequestStack $requestStack)
    {
        $this->request = $requestStack;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return EventReport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Список сообщений о событиях')
            ->setPageTitle('detail', 'Просмотр сообщения от пользователя')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex(),
            TextField::new('name', 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            TextField::new('region', 'Регион'),
            TextField::new('category', 'Категория'),
            ImageField::new('main_photo', 'Картинка')
                ->setBasePath('upload/images/'),
            ImageField::new('additionalPhotos', 'Дополнительные картинки')
                ->setHelp('макс 150кб, и и только jpeg, png, svg или webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setFormTypeOption('multiple', true)
                ->setRequired(false),
            TextField::new('description', 'Описание')->renderAsHtml(false),
            AssociationField::new('reporter', 'Кто сообщил')
                ->setCrudController(UserCrudController::class),
            BooleanField::new('used', 'Использовано')
                ->renderAsSwitch(false)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $url = '/admin/public-event/new?report='. $this->request->getCurrentRequest()->get('entityId');

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::new('new_event', 'Создать событие из этого сообщения')->linkToUrl($url));

        return parent::configureActions($actions);
    }

}

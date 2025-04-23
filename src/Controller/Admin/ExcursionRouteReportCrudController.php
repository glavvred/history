<?php

namespace App\Controller\Admin;

use App\Entity\ExcursionRouteReport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class ExcursionRouteReportCrudController extends AbstractCrudController
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack;

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Список новых маршрутов')
            ->setPageTitle('detail', 'Просмотр маршрута')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues();
    }

    public static function getEntityFqcn(): string
    {
        return ExcursionRouteReport::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            TextField::new('region', 'Регион'),
            ImageField::new('main_photo', 'Картинка')
                ->setBasePath('upload/images/'),
            ImageField::new('additionalPhotos', 'Дополнительные картинки')
                ->setHelp('макс 150кб, и и только jpeg, png, svg или webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setFormTypeOption('multiple', true)
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('route', 'Маршрут'),
            TextField::new('description', 'Описание')->renderAsHtml(false),
            AssociationField::new('reporter', 'Кто сообщил')
                ->setCrudController(UserCrudController::class),
            BooleanField::new('used', 'Использовано')
                ->renderAsSwitch(false)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $url = '/admin/excursion-route/new?report=' . $this->request->getCurrentRequest()->get('entityId');

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::new('new_event', 'Проверить и опубликовать маршрут')->linkToUrl($url));

        return parent::configureActions($actions);
    }


}

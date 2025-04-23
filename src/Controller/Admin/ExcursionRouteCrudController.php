<?php

namespace App\Controller\Admin;

use App\Entity\ExcursionRouteReport;
use App\Entity\ExcursionRoute;
use App\Entity\PublicEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Image;

class ExcursionRouteCrudController extends AbstractCrudController
{
    private $request;
    private $em;

    public function __construct(RequestStack               $requestStack,
                                EntityManagerInterface     $entityManager,
                                readonly RouterInterface $router
    )
    {
        $this->request = $requestStack;
        $this->em = $entityManager;
    }

    public function createEntity(string $entityFqcn): ExcursionRoute
    {
        $excursionRoute = new ExcursionRoute();
        $prefillEntityId = $this->request->getCurrentRequest()->get('report');

        if (!empty($prefillEntityId)) {
            /** @var ExcursionRouteReport $routeReport */
            $routeReport = $this->em->getRepository(ExcursionRouteReport::class)->find($prefillEntityId);
            $excursionRoute->setName($routeReport->getName());
            $excursionRoute->setRegion($routeReport->getRegion());
            $excursionRoute->setShortDescription(nl2br($routeReport->getShortDescription()));
            $excursionRoute->setDescription(nl2br($routeReport->getDescription()));
            $excursionRoute->setMainPhoto($routeReport->getMainPhoto());
            $excursionRoute->setAdditionalPhotos($routeReport->getAdditionalPhotos());
            $excursionRoute->setRoute($routeReport->getRoute());
        }

        return $excursionRoute;
    }


    public static function getEntityFqcn(): string
    {
        return ExcursionRoute::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание нового маршрута')
            ->setPageTitle('edit', 'Редактирование маршрута')
            ->setPageTitle('index', 'Список маршрутов')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->setEntityLabelInPlural('маршруты')
            ->setEntityLabelInSingular('маршрут')
            ->setPaginatorRangeSize(2)
            ->setDefaultSort(['id' => 'DESC'])
            ->hideNullValues();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', label: '#')
                ->hideOnForm(),
            TextField::new('name', label: 'Название')->setRequired(true),
            TextField::new('short_description', label: 'Короткое описание')->setRequired(true),
            TextEditorField::new('description', label: 'Описание')
                ->setTrixEditorConfig([
                    'blockAttributes' => [
                        'default' => ['tagName' => 'p'],
                    ]
                ])
                ->hideOnIndex(),
            ImageField::new('mainPhoto', 'Картинка')
                ->setHelp('макс 3мб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '3m', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired(false)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : []),
            ImageField::new('additionalPhotos', 'Дополнительные картинки')
                ->setHelp('макс 3mb, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setFormTypeOption('multiple', true)
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('route', label: 'Маршрут')
                ->hideOnIndex()
                ->setRequired(true),
            SlugField::new('slug', label: 'ссылка')
                ->setTargetFieldName(['name'])
                ->setUnlockConfirmationMessage('ссылки генерятся автоматически, но если нужно можно и вручную')
                ->hideOnIndex(),
            BooleanField::new('published', label: 'Опубликовано'),

        ];

    }
}

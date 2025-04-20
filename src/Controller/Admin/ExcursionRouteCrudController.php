<?php

namespace App\Controller\Admin;

use App\Entity\ExcursionRoute;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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

    public static function getEntityFqcn(): string
    {
        return ExcursionRoute::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $preview = Action::new('preview', 'Посмотреть', 'fa fa-eye')
            ->linkToUrl(function(ExcursionRoute $route) {

                return $this->router->generate('app_routes_show_slug', [
                    'slug' => $route->getSlug()
                ]);
            });


        return $actions
            ->add(Crud::PAGE_INDEX, $preview)
            ->add(Crud::PAGE_EDIT, $preview)
            ->add(Crud::PAGE_DETAIL, $preview);
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
            TextField::new('shortDescription', label: 'Короткое описание')
                ->setRequired(true)
                ->hideOnIndex(),
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
            BooleanField::new('published', label: 'Опубликовано'),

        ];

    }
}

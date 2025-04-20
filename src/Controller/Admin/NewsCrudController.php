<?php

namespace App\Controller\Admin;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;

#[IsGranted('ROLE_SUPER_ADMIN')]
class NewsCrudController extends AbstractCrudController
{
    private $request;
    private $em;

    public function __construct(RequestStack             $requestStack,
                                EntityManagerInterface   $entityManager,
                                readonly RouterInterface $router
    )
    {
        $this->request = $requestStack;
        $this->em = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание новой новости')
            ->setPageTitle('edit', 'Редактирование новости')
            ->setPageTitle('index', 'Список Новостей')
            ->hideNullValues()
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $preview = Action::new('preview', 'Посмотреть', 'fa fa-eye')
            ->linkToUrl(function (News $news) {

                return $this->router->generate('app_news_show_slug', [
                    'slug' => $news->getSlug()
                ]);
            });


        return $actions
            ->add(Crud::PAGE_INDEX, $preview)
            ->add(Crud::PAGE_EDIT, $preview)
            ->add(Crud::PAGE_DETAIL, $preview)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Создать новость');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', label: 'Название')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            DateField::new('createdAt', label: 'Дата')->setRequired(true),
            TextField::new('url', label: 'Ссылка')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->hideOnIndex(),
            TextEditorField::new('description', label: 'Основной текст новости')
                ->setRequired(true)
                ->hideOnIndex(),
            ImageField::new('photo', 'Картинка')
                ->setHelp('макс 1мб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '1m', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired($pageName !== Crud::PAGE_EDIT)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : []),
            SlugField::new('slug', label: 'ссылка')
                ->setTargetFieldName(['name'])
                ->setUnlockConfirmationMessage('ссылки генерятся автоматически, но если нужно можно и вручную'),
            BooleanField::new('published', label: 'Опубликовано')->setRequired(false)
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $isSlugTaken = $entityManager->getRepository(News::class)->findOneBy(['slug' => $entityInstance->getSlug()]);
        if (!empty($isSlugTaken)) {
            $entityInstance->setSlug($entityInstance->getSlug() . '-' . ($isSlugTaken->getId() + 1));
        }
        parent::persistEntity($entityManager, $entityInstance);
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $isSlugTaken = $entityManager->getRepository(News::class)->findOneBy(['slug' => $entityInstance->getSlug()]);
        if (count($isSlugTaken) > 0) {
            $entityInstance->setSlug($entityInstance->getSlug() . '-' . ($isSlugTaken->getId() + 1));
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}

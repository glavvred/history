<?php

namespace App\Controller\Admin;

use App\Entity\EventCollection;
use App\Entity\PublicEvent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class EventCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventCollection::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Список коллекций событий')
            ->setPageTitle('detail', 'Просмотр коллекции')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues(false);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Название')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            TextField::new('description', 'Описание')
                ->onlyOnForms(),
            TextField::new('shortDescription', 'Короткое описание, 512 макс'),
            ImageField::new('main_photo', 'Картинка')
                ->setBasePath('upload/images/')
                ->setHelp('макс 150кб, и и только jpeg, png, svg, webp и сюда еще доп размерность картинок, чтобы вписались в дизайн')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired($pageName !== Crud::PAGE_EDIT)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : []),
            AssociationField::new('events', 'События в коллекции')
                ->setCrudController(PublicEvent::class),
            BooleanField::new('mainPage', 'Наверху главной')
                ->renderAsSwitch(true),
            BooleanField::new('bottomPage', 'Внизу страниц')
                ->renderAsSwitch(true),
            SlugField::new('slug', label: 'ссылка')
                ->setTargetFieldName('name')
                ->setUnlockConfirmationMessage('ссылки генерятся автоматически, но если нужно можно и вручную')
                ->hideOnIndex(),
            TextField::new('title', 'Заголовок сео')
                ->onlyOnForms(),
            TextField::new('seoDescription', 'Описание сео')
                ->onlyOnForms(),
            ArrayField::new('keywords', 'Ключевые слова')
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);

        return parent::configureActions($actions);
    }


}

<?php

namespace App\Controller\Admin;

use App\Entity\Organisation;
use App\Entity\OrganisationCategory;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class OrganisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Organisation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание новой организации')
            ->setPageTitle('edit', 'Редактирование организации')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->setEntityLabelInSingular('организацию')
            ->setEntityLabelInPlural('Организации')
            ->hideNullValues();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $qb->leftJoin('entity.owner', 'o');
            $qb->where('o.id = :user');
            $qb->setParameter('user', $user);
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setRequired(true)
                ->setHelp('255 макс'),
            AssociationField::new('owner', 'Управляющий')
                ->setCrudController(UserCrudController::class)
                ->autocomplete()
                ->hideOnIndex()
                ->setPermission('ROLE_SUPER_ADMIN'),
            TextField::new('short_description', 'Короткое описание')
                ->setRequired(true)
                ->hideOnIndex(),
            TextEditorField::new('description', 'Описание')
                ->setTrixEditorConfig([
                    'blockAttributes' => [
                        'default' => ['tagName' => 'p'],
                    ]
                ])
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('address', 'Адрес')
                ->setRequired(false)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setHelp('255 макс'),
            ArrayField::new('contacts', label: 'Контакты')
                ->hideOnIndex()
                ->setHelp('Формат телефона +7хххххх (имя). Формат ссылки - ссылка :) ')
                ->setSortable(true)
                ->setRequired(false),
            TextField::new('coordinates', 'Координаты')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setRequired(false)
                ->setHelp('Зайти на яндекс карты, нажать правой кнопкой на точку расположения своей организации, выбрать "что здесь", скопировать координаты'),
            AssociationField::new('category')
                ->setCrudController(OrganisationCategory::class)
                ->setPermission('ROLE_SUPER_ADMIN'),
            ImageField::new('main_photo', 'Картинка')
                ->setRequired($pageName !== Crud::PAGE_EDIT)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : [])
                ->setHelp('макс 250кб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '250k', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            ImageField::new('additionalPhotos', 'Дополнительные картинки')
                ->setHelp('макс 3mb, и и только jpeg, png, svg')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setFormTypeOption('multiple', true)
                ->setRequired(false)
                ->hideOnIndex(),
            BooleanField::new('verified', 'Подтверждена')
                ->setHelp('Неподтвержденную организацию не будет видно в списках')
                ->setPermission('ROLE_SUPER_ADMIN'),
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
}

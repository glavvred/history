<?php

namespace App\Controller\Admin;

use App\Entity\News;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;

#[IsGranted('ROLE_SUPER_ADMIN')]
class NewsCrudController extends AbstractCrudController
{
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
            ->hideNullValues();
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
            TextField::new('url', label: 'Ссылка')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            DateField::new('createdAt', label: 'Дата')->setRequired(true),
            TextEditorField::new('description', label: 'Основной текст новости')->setRequired(true),
            ImageField::new('photo', 'Картинка')
                ->setHelp('макс 1мб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '1m', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired($pageName !== Crud::PAGE_EDIT)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : []),
            SlugField::new('slug', label: 'ссылка')
                ->setTargetFieldName('name')
                ->setUnlockConfirmationMessage('ссылки генерятся автоматически, но если нужно можно и вручную')
                ->hideOnIndex(),
            BooleanField::new('published', label: 'Опубликовано')->setRequired(true)
        ];
    }
}

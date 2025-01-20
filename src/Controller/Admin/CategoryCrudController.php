<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;

#[IsGranted('ROLE_SUPER_ADMIN')]
class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание новой категории')
            ->setPageTitle('edit', 'Редактирование категории')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues()
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setRequired(true),
            TextEditorField::new('description', 'Описание'),
            TextField::new('short', 'Короткое название')
                ->setHelp('только английский, в одно слово')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 15
                    ]
                ]),
            AssociationField::new('parent', 'Уровень выше')
                ->setHelp('Это подкатегория? укажи родительскую тут')
                ->setCrudController(CategoryCrudController::class)
                ->autocomplete(),
            ImageField::new('image', 'Картинка')
                ->setHelp('макс 50кб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '50k', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
        ];
    }

}

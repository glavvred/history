<?php

namespace App\Controller\Admin;

use App\Entity\Filter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class FilterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Filter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание нового фильтра')
            ->setPageTitle('edit', 'Редактирование фильтра')
            ->setPageTitle('index', 'Список фильтров')
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
            TextField::new('short', label: 'Короткое название')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            TextField::new('category', label: 'Категория')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]),
            TextField::new('title', 'Заголовок сео')
                ->onlyOnForms(),
            TextField::new('seoDescription', 'Описание сео')
                ->onlyOnForms(),
            ArrayField::new('keywords', 'Ключевые слова')
                ->onlyOnForms(),
        ];
    }

}

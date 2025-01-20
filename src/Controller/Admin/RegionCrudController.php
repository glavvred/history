<?php

namespace App\Controller\Admin;

use App\Entity\Region;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class RegionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Region::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание нового региона')
            ->setPageTitle('edit', 'Редактирование региона')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->setEntityLabelInSingular('категорию')
            ->setEntityLabelInPlural('категории')
            ->hideNullValues()
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Название')->setRequired(true),
            TextField::new('adminName', 'Название, местный падеж, напр. "в Москве"')->setRequired(true),
            TextField::new('lng', 'Широта')->setHelp('Берем из <a target="new" href="https://yandex.ru/maps/">яндекс-карт</a>, правой кнопкой по точке на карте, "что здесь", слева будут координаты'),
            TextField::new('lat', 'Долгота'),
            AssociationField::new('parent', 'Регион выше')
                ->setCrudController(Region::class),
        ];
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\PublicEvent;
use App\Entity\PublicEventStatistic;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class PublicEventStatisticCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PublicEventStatistic::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('map', 'Карта')->setDisabled(),
            TextField::new('organisation', 'Организация')->setDisabled(),
            TextField::new('button', 'Кнопка')->setDisabled(),
            AssociationField::new('publicEvent', 'Событие')
                ->setCrudController(PublicEvent::class)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);

        return parent::configureActions($actions);
    }
}

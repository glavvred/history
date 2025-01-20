<?php

namespace App\Controller\Admin;

use App\Entity\Organisation;
use App\Entity\OrganisationCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class OrganisationCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganisationCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Категории организаций')
            ->setPageTitle('new', 'Создание новой категории организаций')
            ->setPageTitle('edit', 'Редактирование категории организаций')
            ->hideNullValues()
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('organisations', label: 'Организаций в категории')
                ->setCrudController(Organisation::class)->onlyOnIndex()
                ->setDisabled(),
            TextField::new('name', 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setHelp('255 макс')
                ->setRequired(true),
            TextEditorField::new('description', 'Описание')->setRequired(true)->onlyOnForms(),
            TextField::new('description', 'Описание')
                ->onlyOnIndex()
                ->setMaxLength(100),
            TextField::new('short', 'Короткое название')
                ->setHelp('Только английский, максимально коротко (15 макс), пойдет в адресную строку')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 15
                    ]
                ]),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,  function (Action $action) {
                return $action->setLabel('Новая категория организаций');
            })
            ->remove(Crud::PAGE_INDEX, Action::DELETE);

        return parent::configureActions($actions);
    }

}

<?php

namespace App\Controller\Admin;

use App\Entity\NewsLetter;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class NewsLetterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NewsLetter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание новой рассылки')
            ->setPageTitle('edit', 'Редактирование рассылки')
            ->setPageTitle('index', 'Список рассылок')
            ->showEntityActionsInlined()
            ->hideNullValues();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', label: 'Название')
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255
                    ]
                ])
                ->setRequired(true),
            TextEditorField::new('text', label: 'Основной текст рассылки')
                ->setRequired(true),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendEmails = Action::new('sendEmails', 'Отправить рассылку', 'fa fa-envelope')
            ->linkToRoute('app_send_emails', function (NewsLetter $newsLetter): array {
                return [
                    'id' => $newsLetter->getId(),
                    'name' => $newsLetter->getName(),
                ];
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $sendEmails);
    }
}

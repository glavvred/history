<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;

#[IsGranted('ROLE_SUPER_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание нового пользователя')
            ->setPageTitle('edit', 'Редактирование пользователя')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->hideNullValues(true);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $plainPassword = $entityDto->getInstance()?->getPassword();
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        $this->addEncodePasswordEventListener($formBuilder, $plainPassword);

        return $formBuilder;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    protected function addEncodePasswordEventListener(FormBuilderInterface $formBuilder, $plainPassword = null): void
    {
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($plainPassword) {
            /** @var User $user */
            $user = $event->getData();
            if ($user->getPassword() !== $plainPassword) {
                $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
            }
        });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Имя')
                ->setRequired(true),
            TextField::new('email', 'Почта')->setRequired(true)->onlyWhenUpdating()->setDisabled(),
            TextField::new('email', 'Почта')->setRequired(true)->onlyWhenCreating(),
            ChoiceField::new('roles', 'Роли')
                ->allowMultipleChoices()
                ->setChoices([
                        'пользователь' => User::ROLE_USER,
                        'АДМИНИСТРАТОР' => User::ROLE_SUPER_ADMIN,
                        'администратор организации' => User::ROLE_ADMIN,
                    ]
                )
                ->renderAsBadges(),
            ArrayField::new('organisations', 'Организации')
                ->onlyOnIndex()
                ->setDisabled(),
            ImageField::new('avatar', 'Аватар')
                ->setSortable(false)
                ->setHelp('макс 250кб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '50k', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            TextField::new('password', 'New password')->onlyWhenCreating()->setRequired(true)
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'New password'],
                    'second_options' => ['label' => 'Repeat password'],
                    'error_bubbling' => true,
                    'invalid_message' => 'The password fields do not match.',
                ]),
            TextField::new('password', 'New password')->onlyWhenUpdating()->setRequired(false)
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'New password'],
                    'second_options' => ['label' => 'Repeat password'],
                    'error_bubbling' => true,
                    'invalid_message' => 'The password fields do not match.',
                ])
        ];
    }
}

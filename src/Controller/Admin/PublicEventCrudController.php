<?php

namespace App\Controller\Admin;

use App\Entity\EventReport;
use App\Entity\Filter;
use App\Entity\PublicEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class PublicEventCrudController extends AbstractCrudController
{
    private $request;
    private $em;

    public function __construct(RequestStack           $requestStack,
                                EntityManagerInterface $entityManager)
    {
        $this->request = $requestStack;
        $this->em = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return PublicEvent::class;
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $qb->leftJoin('entity.organisation', 'org');
            $qb->leftJoin('org.owner', 'o');
            $qb->where('o.id = :user');
            $qb->setParameter('user', $user);
        }

        return $qb;
    }


    public function createEntity(string $entityFqcn): PublicEvent
    {
        $publicEvent = new PublicEvent();
        $publicEvent->setCreatedAt(new DateTimeImmutable());
        $publicEvent->setOwner($this->getUser());

        $prefillEntityId = $this->request->getCurrentRequest()->get('report');

        if (!empty($prefillEntityId)) {
            /** @var EventReport $eventReport */
            $eventReport = $this->em->getRepository(EventReport::class)->find($prefillEntityId);
            $publicEvent->setOwner($eventReport->getReporter());
            $publicEvent->setName($eventReport->getName());
            $publicEvent->setStartDate($eventReport->getStartDate());
            $publicEvent->setDuration($eventReport->getDuration());
            $publicEvent->setRegion($eventReport->getRegion());
            $publicEvent->setCategory($eventReport->getCategory());
            $publicEvent->setAddress($eventReport->getAddress());
            $publicEvent->setDescription($eventReport->getDescription());
            $publicEvent->setMainPhoto($eventReport->getMainPhoto());
            $publicEvent->setUrl($eventReport->getLink());
            $publicEvent->setPrequisites($eventReport->getPrequisites());
            $publicEvent->setToll($eventReport->getToll());
        }

        return $publicEvent;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $prefillEntityId = $this->request->getCurrentRequest()->get('report');
        if (!empty($prefillEntityId)) {
            /** @var EventReport $eventReport */
            $eventReport = $this->em->getRepository(EventReport::class)->find($prefillEntityId);
            $eventReport->setUsed(true);
            $this->em->persist($eventReport);
            $this->em->flush();
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Создание нового события')
            ->setPageTitle('edit', 'Редактирование события')
            ->setPageTitle('index', 'Список событий')
            ->setHelp('new', 'Тут будет какой то хелп')
            ->setEntityLabelInPlural('События')
            ->setEntityLabelInSingular('событие')
            ->setPaginatorRangeSize(2)
            ->hideNullValues();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', label: '#')
                ->hideOnForm(),
            DateField::new('createdAt', label: 'Создано')
                ->hideOnForm(),
            AssociationField::new('owner', label: 'Создатель')
                ->autocomplete()
                ->setCrudController(UserCrudController::class),
            AssociationField::new('organisation', label: 'Организатор')
                ->autocomplete()
                ->setCrudController(OrganisationCrudController::class),
            TextField::new('name', label: 'Название')->setRequired(true),
            TextField::new('shortDescription', label: 'Короткое описание')
                ->setRequired(true)
                ->hideOnIndex(),
            DateField::new('startDate', label: 'Дата начала')->setRequired(true),
            IntegerField::new('duration', label: 'Продолжительность'),
            BooleanField::new('constant', label: 'Постоянное мероприятие'),
            AssociationField::new('region', label: 'Регион')
                ->autocomplete()
                ->setCrudController(RegionCrudController::class)
                ->setRequired(true),
            AssociationField::new('category', label: 'Категория')
                ->autocomplete()
                ->setCrudController(CategoryCrudController::class)
                ->setRequired(true),
            TextField::new('address', label: 'Адрес')
                ->setHelp('Включен автокомплит')
                ->setHtmlAttribute('data-id', 'autocomplete-address-input')
                ->setRequired(true)
                ->hideOnIndex(),
            TextEditorField::new('description', label: 'Описание')
                ->hideOnIndex(),
            ImageField::new('mainPhoto', 'Картинка')
                ->setHelp('макс 3мб, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setFileConstraints(new Image(maxSize: '3m', mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp']))
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired(false)
                ->setFormTypeOptions($pageName == Crud::PAGE_EDIT ? ['allow_delete' => false] : []),
            TextField::new('vk', label: 'vk')
                ->hideOnIndex()
                ->setSortable(true)
                ->setRequired(false),
            TextField::new('tg', label: 'tg')
                ->hideOnIndex()
                ->setSortable(true)
                ->setRequired(false),
            TextField::new('url', label: 'Ссылка, в красную кнопку')
                ->hideOnIndex()
                ->setSortable(true)
                ->setRequired(false),
            TextField::new('url_text', label: 'Текст на красной кнопке')
                ->hideOnIndex()
                ->setSortable(true)
                ->setRequired(false),
            ImageField::new('additionalPhotos', 'Дополнительные картинки')
                ->setHelp('макс 3mb, и и только jpeg, png, svg, webp')
                ->setBasePath('upload/images/')
                ->setUploadDir('public_html/upload/images/')
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setFormTypeOption('multiple', true)
                ->setRequired(false)
                ->hideOnIndex(),
            TextEditorField::new('prequisites', label: 'Требования')
                ->setHelp('255')
                ->hideOnIndex(),
            TextEditorField::new('toll', label: 'Платное?')
                ->setHelp('512')
                ->hideOnIndex(),
            AssociationField::new('filter', label: 'Фильтры')
                ->setCrudController(FilterController::class)
                ->setRequired(false)
                ->hideOnIndex(),
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


    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            ->addJsFile('https://code.jquery.com/jquery-3.6.0.min.js')
            ->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.2.1/typeahead.bundle.min.js')
            ->addHtmlContentToHead("<script>
                $(document).ready(function () {
                    let addressSuggestions = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: '/autocomplete/address?query=%QUERY',
                            wildcard: '%QUERY',
                            rateLimitBy : 'debounce',
                            rateLimitWait : 600        
                        }
                    });
        
                    $('#PublicEvent_address').typeahead(null, {
                        name: 'address',
                        display: 'value',
                        source: addressSuggestions
                    });
                });
            </script>")
            ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/typeahead.js-bootstrap-css/1.2.1/typeaheadjs.min.css')
//            ->addJsFile('admin/jquery-3.7.1.min.js')
//            ->addJsFile('admin/featherlight.min.js')
//            ->addJsFile('admin/override.js')
            ;
    }


}

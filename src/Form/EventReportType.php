<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\EventReport;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class EventReportType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'empty_data' => '',
                'required' => true,
            ])
            ->add('duration', IntegerType::class, [
                'invalid_message' => 'Число должно быть целое',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'required' => true
            ])
            ->add('link', UrlType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('mainPhotoFile', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5m',
                        'mimeTypes' => [
                            'image/jpeg', 'image/png', 'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'макс 250кб, и и только jpeg, png, svg',
                    ])
                ],
            ])
            ->add('additionalPhotosFiles', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'attr'     => [
                    'multiple' => 'multiple',
                    'mimeTypes' => [
                        'image/jpeg', 'image/png', 'image/svg+xml'
                    ],
                    'mimeTypesMessage' => 'макс 250кб, и и только jpeg, png, svg',
                ]
            ])
            ->add('prequisites', TextType::class, [
                'required' => false,
            ])
            ->add('toll', TextType::class, [
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select']
            ])
            ->add('region', EntityType::class, [
                'required' => false,
                'class' => Region::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select']
            ])
            ->add('captcha', CaptchaType::class, [
                'required'=> true,
                'attr' => ['class' => 'form-control']
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventReport::class,
            'csrf_protection' => false,
        ]);
    }
}

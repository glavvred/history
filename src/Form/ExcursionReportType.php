<?php

namespace App\Form;

use App\Entity\ExcursionRouteReport;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class ExcursionReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('shortDescription', TextareaType::class)
            ->add('description', TextareaType::class)
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
            ->add('route')
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExcursionRouteReport::class,
            'csrf_protection' => false,
        ]);
    }
}

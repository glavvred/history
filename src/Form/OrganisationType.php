<?php

namespace App\Form;

use App\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('coordinates')
            ->add('category')
            ->add('main_photo_file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '250k',
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
                'attr' => [
                    'multiple' => 'multiple',
                    'mimeTypes' => [
                        'image/jpeg', 'image/png', 'image/svg+xml'
                    ],
                    'mimeTypesMessage' => 'макс 250кб, и и только jpeg, png, svg',
                ]
            ])
            ->add('description')
            ->add('contacts');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}

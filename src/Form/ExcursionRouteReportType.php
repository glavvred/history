<?php

namespace App\Form;

use App\Entity\ExcursionRouteReport;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcursionRouteReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('mainPhoto')
            ->add('additionalPhotos')
            ->add('route')
            ->add('used')
            ->add('reporter', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExcursionRouteReport::class,
        ]);
    }
}

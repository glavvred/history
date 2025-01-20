<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\EventCollection;
use App\Entity\PublicEvent;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDate', null, [
                'widget' => 'single_text',
            ])
            ->add('duration')
            ->add('address')
            ->add('link')
            ->add('description')
            ->add('mainPhoto')
            ->add('additionalPhotos')
            ->add('prequisites')
            ->add('toll')
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'id',
            ])
            ->add('collections', EntityType::class, [
                'class' => EventCollection::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicEvent::class,
        ]);
    }
}

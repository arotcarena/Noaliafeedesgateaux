<?php

namespace App\Form;

use App\DataModel\OrderSearchFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Plus récentes d\'abord' => 'latest',
                    'Plus anciennes d\'abord' => 'oldest'
                ]
            ])
            ->add('seen', ChoiceType::class, [
                'choices' => [
                    'Non lues' => '0',
                    'Toutes' => '1'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderSearchFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

<?php

namespace App\Form;

use App\DataModel\CakeSearchFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CakeSearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class)
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Plus récents d\'abord' => 'latest',
                    'Plus anciens d\'abord' => 'oldest',
                    'Plus vus' => 'maxVisited',
                    'Moins vus' => 'minVisited'
                ]
            ])
            ->add('spotlighted', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CakeSearchFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

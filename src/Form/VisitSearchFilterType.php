<?php

namespace App\Form;

use App\DataModel\VisitSearchFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitSearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('period', ChoiceType::class, [
                'choices' => [
                    'Toutes les périodes' => null,
                    'Aujourd\'hui' => VisitSearchFilter::PERIOD_TODAY,
                    'Les 7 derniers jours' => VisitSearchFilter::PERIOD_LAST_WEEK,
                    'Depuis 1 mois' => VisitSearchFilter::PERIOD_LAST_MONTH,
                    'Depuis 3 mois' => VisitSearchFilter::PERIOD_LAST_THREE_MONTHS,
                    'Depuis 1 an' => VisitSearchFilter::PERIOD_LAST_YEAR 
                ]
            ])
            ->add('pageId', ChoiceType::class, [
                'choices' => [
                    'Toutes les pages' => null,
                    'Page d\'Accueil' => VisitSearchFilter::PAGE_HOME,
                    'Toutes mes créations' => VisitSearchFilter::PAGE_CAKES
                ]
            ])
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Plus récentes d\'abord' => VisitSearchFilter::SORT_LATEST,
                    'Plus anciennes d\'abord' => VisitSearchFilter::SORT_OLDEST
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VisitSearchFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\ExportCsvQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportCsvQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sorting', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQuerySortingType::class,
            'entry_options' => [
                'field_choices' => $options['sorting_field_choices'],
                'documentation' => [
                    'groups' => $options['documentation']['groups'] ?? null,
                ],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ListQuerySortingType that have different field_choices set
            'documentation' => ListQuerySortingType::getDocumentation($options['sorting_field_choices']),
        ]);
        $builder->add('filters', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQueryFilterType::class,
            'entry_options' => [
                'filterBy_choices' => $options['filter_filterBy_choices'],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ListQueryFilterType that have different filterBy_choices set
            'documentation' => ListQueryFilterType::getDocumentation($options['filter_filterBy_choices']),
        ]);
        $builder->add('fields', CollectionType::class, [
            'required' => false,
            'entry_type' => ExportQueryFieldType::class,
            'entry_options' => [
                'field_choices' => $options['fields_field_choices'],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ExportQueryFieldType that have different filterBy_choices set
            'documentation' => ExportQueryFieldType::getDocumentation($options['fields_field_choices']),
        ]);
        $builder->add('filename', null, [
            'documentation' => [
                'description' => 'Filename for exported CSV',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportCsvQuery::class,
            'sorting_field_choices' => [],
            'filter_filterBy_choices' => [],
            'fields_field_choices' => [],
        ]);
    }
}

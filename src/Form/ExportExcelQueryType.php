<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\ExportExcelQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportExcelQueryType extends AbstractType
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
            'documentation' => [
                'description' => 'List of sorting definitions',
            ],
        ]);
        $builder->add('filters', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQueryFilterType::class,
            'entry_options' => [
                'filterBy_choices' => $options['filter_filterBy_choices'],
                'documentation' => [
                    'groups' => $options['documentation']['groups'] ?? null,
                ],
            ],
            'allow_add' => true,
            'documentation' => [
                'description' => 'List of filter definitions',
            ],
        ]);
        $builder->add('fields', CollectionType::class, [
            'required' => false,
            'entry_type' => ExportQueryFieldType::class,
            'entry_options' => [
                'field_choices' => $options['fields_field_choices'],
                'documentation' => [
                    'groups' => $options['documentation']['groups'] ?? null,
                ],
            ],
            'allow_add' => true,
            'documentation' => [
                'description' => 'List of fields to export',
            ],
        ]);
        $builder->add('filename', null, [
            'documentation' => [
                'description' => 'Filename for exported Excel',
            ],
        ]);
        $builder->add('sheetName', null, [
            'documentation' => [
                'description' => 'Sheet name for exported Excel',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportExcelQuery::class,
            'sorting_field_choices' => [],
            'filter_filterBy_choices' => [],
            'fields_field_choices' => [],
        ]);
    }
}

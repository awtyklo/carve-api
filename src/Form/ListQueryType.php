<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Enum\ListQueryFilterType as ListQueryFilterTypeEnum;
use Carve\ApiBundle\Enum\ListQuerySortingDirection;
use Carve\ApiBundle\Model\ListQuery;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('page', IntegerType::class, [
            'invalid_message' => 'validation.integer',
            'documentation' => [
                'type' => 'integer',
                'example' => 1,
                'description' => 'Page number to return (pages start at 1)',
            ],
        ]);
        $builder->add('rowsPerPage', IntegerType::class, [
            'invalid_message' => 'validation.integer',
            'documentation' => [
                'type' => 'integer',
                'example' => 10,
                'description' => 'Number of results per page',
            ],
        ]);
        $builder->add('sorting', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQuerySortingType::class,
            'entry_options' => [
                'field_choices' => $options['sorting_field_choices'],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ListQuerySortingType that have different field_choices set
            'documentation' => [
                'type' => 'array',
                'description' => 'List of sorting definitions',
                'items' => new OA\Schema([
                    'type' => 'object',
                    'required' => [
                        'field',
                        'direction',
                    ],
                    'properties' => [
                        new OA\Property([
                            'type' => 'string',
                            'property' => 'field',
                            'enum' => $options['sorting_field_choices'],
                            'description' => 'Field to sort by',
                        ]),
                        new OA\Property([
                            'type' => 'string',
                            'property' => 'direction',
                            'enum' => ListQuerySortingDirection::cases(),
                            'description' => 'Sorting direction',
                        ]),
                    ],
                ]),
            ],
        ]);
        $builder->add('filters', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQueryFilterType::class,
            'entry_options' => [
                'filterBy_choices' => $options['filter_filterBy_choices'],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ListQueryFilterType that have different filterBy_choices set
            'documentation' => [
                'type' => 'array',
                'description' => 'List of filter definitions',
                'items' => new OA\Schema([
                    'type' => 'object',
                    'required' => [
                        'filterBy',
                        'filterType',
                        'filterValue',
                    ],
                    'properties' => [
                        new OA\Property([
                            'type' => 'string',
                            'property' => 'filterBy',
                            'enum' => $options['filter_filterBy_choices'],
                            'description' => 'Field to filter by',
                        ]),
                        new OA\Property([
                            'type' => 'string',
                            'property' => 'filterType',
                            'enum' => ListQueryFilterTypeEnum::cases(),
                            'description' => 'Filter type',
                            'example' => 'equal',
                        ]),
                        new OA\Property([
                            'type' => 'string',
                            'property' => 'filterValue',
                            'description' => 'Filter value. Depending on filterType it can be string (includes dates), number, integer, boolean, or array',
                            'example' => [
                                'John',
                                12,
                                '2023-06-13T15:30:11+02:00',
                                true,
                                ['John', 'Mike'],
                            ],
                        ]),
                    ],
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListQuery::class,
            'sorting_field_choices' => [],
            'filter_filterBy_choices' => [],
        ]);
    }
}

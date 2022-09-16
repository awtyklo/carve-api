<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\ListQuery;
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
            'documentation' => [
                'type' => 'integer',
                'example' => 1,
                'description' => 'Page number to return (pages start at 1)',
            ],
        ]);
        $builder->add('rowsPerPage', IntegerType::class, [
            'documentation' => [
                'type' => 'integer',
                'example' => 10,
                'description' => 'Number of results per page',
            ],
        ]);
        $builder->add('sorting', CollectionType::class, [
            'entry_type' => ListQuerySortingType::class,
            'entry_options' => [
                'field_choices' => $options['sorting_field_choices'],
            ],
            'allow_add' => true,
        ]);
        $builder->add('filters', CollectionType::class, [
            'entry_type' => ListQueryFilterType::class,
            'allow_add' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListQuery::class,
            'sorting_field_choices' => [],
        ]);
    }
}

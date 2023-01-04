<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\BatchQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchQueryType extends AbstractType
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
        $builder->add('ids', CollectionType::class, [
            'allow_add' => true,
            'error_bubbling' => false,
            'documentation' => [
                'description' => 'List of selected ids',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BatchQuery::class,
            'sorting_field_choices' => [],
        ]);
    }
}

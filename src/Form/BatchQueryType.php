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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sorting', CollectionType::class, [
            'required' => false,
            'entry_type' => ListQuerySortingType::class,
            'entry_options' => [
                'field_choices' => $options['sorting_field_choices'],
            ],
            'allow_add' => true,
            // We need to prepare documentation by hand because Nelmio is not distinguish ListQuerySortingType that have different field_choices set
            'documentation' => ListQuerySortingType::getDocumentation($options['sorting_field_choices']),
        ]);
        $builder->add('ids', CollectionType::class, [
            'allow_add' => true,
            'error_bubbling' => false,
            'documentation' => [
                'description' => 'List of selected ids',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BatchQuery::class,
            'sorting_field_choices' => [],
        ]);
    }
}

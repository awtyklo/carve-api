<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Enum\ListQuerySortingDirection;
use Carve\ApiBundle\Model\ListQuerySorting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListQuerySortingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('field', ChoiceType::class, [
            'choices' => $options['field_choices'],
            'invalid_message' => 'validation.choice',
            'documentation' => [
                'description' => 'Field to sort by',
            ],
        ]);
        $builder->add('direction', EnumType::class, [
            'class' => ListQuerySortingDirection::class,
            'invalid_message' => 'validation.enum',
            'documentation' => [
                'description' => 'Sorting direction',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListQuerySorting::class,
            'field_choices' => [],
        ]);
    }
}

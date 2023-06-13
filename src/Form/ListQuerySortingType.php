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
        // Note: Fields for this form are documented for Nelmio in ListQuerySortingType. Read more there.
        $builder->add('field', ChoiceType::class, [
            'choices' => $options['field_choices'],
            'invalid_message' => 'validation.choice',
        ]);
        $builder->add('direction', EnumType::class, [
            'class' => ListQuerySortingDirection::class,
            'invalid_message' => 'validation.enum',
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

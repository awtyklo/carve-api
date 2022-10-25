<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\ExportQueryField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportQueryFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('field', ChoiceType::class, [
            'choices' => $options['field_choices'],
            'invalid_message' => 'validation.choice',
            'documentation' => [
                'description' => 'Field to export',
            ],
        ]);
        $builder->add('label', null, [
            'documentation' => [
                'description' => 'Field label in exported file',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportQueryField::class,
            'field_choices' => [],
        ]);
    }
}

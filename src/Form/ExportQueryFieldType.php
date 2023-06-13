<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Model\ExportQueryField;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportQueryFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Note: Fields for this form are documented for Nelmio in ListQuerySortingType. Read more there.
        $builder->add('field', ChoiceType::class, [
            'choices' => $options['field_choices'],
            'invalid_message' => 'validation.choice',
        ]);
        $builder->add('label');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportQueryField::class,
            'field_choices' => [],
        ]);
    }

    public static function getDocumentation(array $options): array
    {
        return [
            'type' => 'array',
            'description' => 'List of fields to export',
            'items' => new OA\Schema([
                'type' => 'object',
                'required' => [
                    'field',
                    'label',
                ],
                'properties' => [
                    new OA\Property([
                        'type' => 'string',
                        'property' => 'field',
                        'enum' => $options,
                        'description' => 'Field to export',
                    ]),
                    new OA\Property([
                        'type' => 'string',
                        'property' => 'label',
                        'description' => 'Field label in exported file',
                    ]),
                ],
            ]),
        ];
    }
}

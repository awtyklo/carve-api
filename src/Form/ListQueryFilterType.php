<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Enum\ListQueryFilterType as ListQueryFilterTypeEnum;
use Carve\ApiBundle\Model\ListQueryFilter;
use Carve\ApiBundle\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class ListQueryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('filterBy', null, [
            'documentation' => [
                'type' => 'string',
                'example' => 'myFieldName',
                'description' => 'Field to filter by',
            ],
        ]);
        $builder->add('filterType', EnumType::class, [
            'class' => ListQueryFilterTypeEnum::class,
            'documentation' => [
                'description' => 'Filter type',
                'example' => 'like',
            ],
        ]);
        $builder->get('filterType')->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);

        // Field is added here just for Nelmio
        $builder->add('filterValue', null, [
            'documentation' => [
                'description' => 'Filter value. Depending on filterType it can be boolean, string, number, date, datetime or array',
                'type' => ['mixed'],
                'example' => 'string',
            ],
        ]);
    }

    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm()->getParent();
        $filterType = $event->getData();

        $options = [
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ];

        switch ($filterType) {
            case ListQueryFilterTypeEnum::BOOLEAN:
                $form->add('filterValue', CheckboxType::class);
                break;
            case ListQueryFilterTypeEnum::EQUAL:
            case ListQueryFilterTypeEnum::LIKE:
                $form->add('filterValue', TextType::class, $options);
                break;
            case ListQueryFilterTypeEnum::EQUALMULTIPLE:
                $form->add('filterValue', CollectionType::class, [
                    'entry_type' => ListQueryFiltersMultipleValueType::class,
                    'allow_add' => true,
                    'error_bubbling' => false,
                    'constraints' => [
                        new Count([
                            'min' => 1,
                            'minMessage' => 'validation.required',
                        ]),
                    ],
                ]);
                break;
            case ListQueryFilterTypeEnum::DATEGREATERTHAN:
            case ListQueryFilterTypeEnum::DATEGREATERTHANOREQUAL:
            case ListQueryFilterTypeEnum::DATELESSTHAN:
            case ListQueryFilterTypeEnum::DATELESSTHANOREQUAL:
            case ListQueryFilterTypeEnum::DATETIMEGREATERTHAN:
            case ListQueryFilterTypeEnum::DATETIMEGREATERTHANOREQUAL:
            case ListQueryFilterTypeEnum::DATETIMELESSTHAN:
            case ListQueryFilterTypeEnum::DATETIMELESSTHANOREQUAL:
            case ListQueryFilterTypeEnum::TIMEGREATERTHAN:
            case ListQueryFilterTypeEnum::TIMEGREATERTHANOREQUAL:
            case ListQueryFilterTypeEnum::TIMELESSTHAN:
            case ListQueryFilterTypeEnum::TIMELESSTHANOREQUAL:
                $form->add('filterValue', DateTimeType::class, $options);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListQueryFilter::class,
        ]);
    }
}

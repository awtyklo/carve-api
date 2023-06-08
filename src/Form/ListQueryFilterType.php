<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Form;

use Carve\ApiBundle\Enum\ListQueryFilterType as ListQueryFilterTypeEnum;
use Carve\ApiBundle\Form\Type\DateTimeType;
use Carve\ApiBundle\Model\ListQueryFilter;
use Carve\ApiBundle\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListQueryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('filterBy', ChoiceType::class, [
            'choices' => $options['filterBy_choices'],
            'invalid_message' => 'validation.choice',
            'documentation' => [
                'description' => 'Field to filter by',
            ],
        ]);
        $builder->add('filterType', EnumType::class, [
            'class' => ListQueryFilterTypeEnum::class,
            'invalid_message' => 'validation.enum',
            'documentation' => [
                'description' => 'Filter type',
                'example' => 'equal',
            ],
        ]);

        $filterValueFormModifier = function (FormInterface $form, ?ListQueryFilterTypeEnum $filterType = null) {
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
                case ListQueryFilterTypeEnum::STARTSWITH:
                case ListQueryFilterTypeEnum::ENDSWITH:
                    $form->add('filterValue', TextType::class, $options);
                    break;
                case ListQueryFilterTypeEnum::EQUALMULTIPLE:
                    $form->add('filterValue', CollectionType::class, [
                        'allow_add' => true,
                        'error_bubbling' => false,
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
        };

        $builder->get('filterType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($filterValueFormModifier) {
                $filterType = $event->getForm()->getData();

                $filterValueFormModifier($event->getForm()->getParent(), $filterType);
            }
        );

        // Field is added here just for Nelmio
        $builder->add('filterValue', null, [
            'documentation' => [
                'description' => 'Filter value. Depending on filterType it can be string (includes dates), number, integer, boolean, or array',
                'type' => 'string',
                'example' => [
                    'John',
                    12,
                    '2023-06-13T15:30:11+02:00',
                    true,
                    ['John', 'Mike'],
                ],
            ],
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($filterValueFormModifier) {
                $filter = $event->getData();

                $filterValueFormModifier($event->getForm(), $filter ? $filter->getFilterType() : null);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListQueryFilter::class,
            'filterBy_choices' => [],
        ]);
    }
}

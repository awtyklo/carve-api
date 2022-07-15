<?php

namespace Carve\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as BaseDateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'model_timezone' => 'UTC',
            'view_timezone' => 'UTC',
            'format' => 'yyyy-MM-dd',
            'invalid_message' => 'validation.invalid.date',
        ]);
    }

    public function getParent()
    {
        return BaseDateType::class;
    }
}

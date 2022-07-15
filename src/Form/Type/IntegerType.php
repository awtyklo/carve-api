<?php

namespace Carve\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as BaseIntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'validation.invalid.number',
        ]);
    }

    public function getParent()
    {
        return BaseIntegerType::class;
    }
}

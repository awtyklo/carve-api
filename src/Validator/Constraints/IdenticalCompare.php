<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IdenticalCompare extends Constraint
{
    public $message = 'validation.identicalCompare';
    public $propertyPath1;
    public $propertyPath2;

    public function getRequiredOptions()
    {
        return [
            'propertyPath1',
            'propertyPath2',
        ];
    }
}

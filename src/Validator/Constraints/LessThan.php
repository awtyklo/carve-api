<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class LessThan extends Assert\LessThan
{
    public $message = 'validation.lessThan';

    public function validatedBy()
    {
        return Assert\LessThanValidator::class;
    }
}

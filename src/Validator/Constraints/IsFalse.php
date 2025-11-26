<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsFalse extends Assert\IsFalse
{
    public $message = 'validation.isFalse';

    public function validatedBy(): string
    {
        return Assert\IsFalseValidator::class;
    }
}

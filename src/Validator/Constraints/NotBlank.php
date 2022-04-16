<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlank extends Assert\NotBlank
{
    public $message = 'validation.required';

    public function validatedBy()
    {
        return Assert\NotBlankValidator::class;
    }
}

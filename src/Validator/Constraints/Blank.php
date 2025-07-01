<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Blank extends Assert\Blank
{
    public string $message = 'validation.blank';

    public function validatedBy(): string
    {
        return Assert\BlankValidator::class;
    }
}

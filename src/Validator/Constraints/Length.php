<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Length extends Assert\Length
{
    public $minMessage = 'validation.lengthMin';
    public $maxMessage = 'validation.lengthMax';

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

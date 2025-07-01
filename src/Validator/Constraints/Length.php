<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Length extends Assert\Length
{
    public string $maxMessage = 'validation.lengthMax';
    public string $minMessage = 'validation.lengthMin';
    public string $exactMessage = 'validation.lengthExact';

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Length extends Assert\Length
{
    public $maxMessage = 'validation.lengthMax';
    public $minMessage = 'validation.lengthMin';
    public $exactMessage = 'validation.lengthExact';
    public $charsetMessage = 'validation.lengthCharset';

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Email extends Assert\Email
{
    public $message = 'validation.email';

    public function validatedBy(): string
    {
        return Assert\EmailValidator::class;
    }
}

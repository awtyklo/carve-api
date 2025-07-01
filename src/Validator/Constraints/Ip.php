<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Ip extends Assert\Ip
{
    public string $message = 'validation.ip';

    public function validatedBy(): string
    {
        return Assert\IpValidator::class;
    }
}

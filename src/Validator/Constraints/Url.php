<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Url extends Assert\Url
{
    public $message = 'validation.invalidUrl';

    public function validatedBy()
    {
        return Assert\Urlalidator::class;
    }
}

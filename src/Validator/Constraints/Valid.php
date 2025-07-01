<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This is direct use of existing Valid assert. Created only for developer convenience.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Valid extends Assert\Valid
{
    public function validatedBy(): string
    {
        return Assert\ValidValidator::class;
    }
}

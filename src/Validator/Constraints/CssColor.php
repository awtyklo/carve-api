<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CssColor extends Assert\CssColor
{
    public function __construct($formats = [], ?string $message = 'validation.cssColor', ?array $groups = null, $payload = null, ?array $options = null)
    {
        parent::__construct($formats, $message, $groups, $payload, $options);
    }

    public function validatedBy(): string
    {
        return Assert\CssColorValidator::class;
    }
}

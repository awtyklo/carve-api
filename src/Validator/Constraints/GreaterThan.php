<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class GreaterThan extends Assert\GreaterThan
{
    public function __construct(mixed $value = null, ?string $propertyPath = null, ?string $message = 'validation.greaterThan', ?array $groups = null, mixed $payload = null, array $options = [])
    {
        parent::__construct(
            $value,
            $propertyPath,
            $message,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return Assert\GreaterThanValidator::class;
    }
}

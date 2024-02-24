<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IntegerType extends Assert\Type
{
    public function __construct(string|array|null $type = 'integer', ?string $message = 'validation.integerType', ?array $groups = null, mixed $payload = null, array $options = [])
    {
        parent::__construct(
            $type,
            $message,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return Assert\TypeValidator::class;
    }
}

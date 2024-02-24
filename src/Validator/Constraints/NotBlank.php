<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlank extends Assert\NotBlank
{
    public function __construct(?array $options = null, ?string $message = 'validation.required', ?bool $allowNull = null, ?callable $normalizer = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(
            $options,
            $message,
            $allowNull,
            $normalizer,
            $groups,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\NotBlankValidator::class;
    }
}

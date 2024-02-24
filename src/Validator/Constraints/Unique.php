<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Unique extends Assert\Unique
{
    public function __construct(
        ?array $options = null,
        ?string $message = 'validation.unique',
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null,
        array|string|null $fields = null,
    ) {
        parent::__construct(
            $options,
            $message,
            $normalizer,
            $groups,
            $payload,
            $fields,
        );
    }

    public function validatedBy(): string
    {
        return Assert\UniqueValidator::class;
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class OrmStringType extends Length
{
    #[HasNamedArguments]
    public function __construct(
        int|array|null $exactly = null,
        ?int $min = null,
        ?int $max = null,
        ?string $charset = null,
        ?callable $normalizer = null,
        ?string $countUnit = null,
        ?string $exactMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?string $charsetMessage = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(
            exactly: $exactly,
            min: $min,
            max: $max ?? 255,
            charset: $charset,
            normalizer: $normalizer,
            exactMessage: $exactMessage,
            minMessage: $minMessage,
            maxMessage: $maxMessage,
            charsetMessage: $charsetMessage,
            groups: $groups,
            payload: $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

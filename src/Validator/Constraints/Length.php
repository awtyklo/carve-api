<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Length extends Assert\Length
{
    public function __construct(
        int|array|null $exactly = null,
        ?int $min = null,
        ?int $max = null,
        ?string $charset = null,
        ?callable $normalizer = null,
        ?string $countUnit = null,
        ?string $exactMessage = null,
        ?string $minMessage = 'validation.lengthMin',
        ?string $maxMessage = 'validation.lengthMax',
        ?string $charsetMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct(
            $exactly,
            $min,
            $max,
            $charset,
            $normalizer,
            $countUnit,
            $exactMessage,
            $minMessage,
            $maxMessage,
            $charsetMessage,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

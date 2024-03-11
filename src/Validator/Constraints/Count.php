<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Count extends Assert\Count
{
    public function __construct(
        int|array|null $exactly = null,
        ?int $min = null,
        ?int $max = null,
        ?int $divisibleBy = null,
        ?string $exactMessage = 'validation.countExact',
        ?string $minMessage = 'validation.countMin',
        ?string $maxMessage = 'validation.countMax',
        ?string $divisibleByMessage = 'validation.countDivisibleBy',
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct(
            $exactly,
            $min,
            $max,
            $divisibleBy,
            $exactMessage,
            $minMessage,
            $maxMessage,
            $divisibleByMessage,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return Assert\CountValidator::class;
    }
}

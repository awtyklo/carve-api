<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Range extends Assert\Range
{
    public function __construct(
        ?array $options = null,
        ?string $notInRangeMessage = 'validation.rangeNotIn',
        ?string $minMessage = 'validation.rangeMin',
        ?string $maxMessage = 'validation.rangeMax',
        ?string $invalidMessage = 'validation.rangeNumber',
        ?string $invalidDateTimeMessage = 'validation.rangeDateTime',
        mixed $min = null,
        ?string $minPropertyPath = null,
        mixed $max = null,
        ?string $maxPropertyPath = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct(
            $options,
            $notInRangeMessage,
            $minMessage,
            $maxMessage,
            $invalidMessage,
            $invalidDateTimeMessage,
            $min,
            $minPropertyPath,
            $max,
            $maxPropertyPath,
            $groups,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\RangeValidator::class;
    }
}

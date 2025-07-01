<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Range extends Assert\Range
{
    public string $notInRangeMessage = 'validation.rangeNotIn';
    public string $minMessage = 'validation.rangeMin';
    public string $maxMessage = 'validation.rangeMax';
    public string $invalidMessage = 'validation.rangeNumber';
    public string $invalidDateTimeMessage = 'validation.rangeDateTime';

    public function validatedBy(): string
    {
        return Assert\RangeValidator::class;
    }
}

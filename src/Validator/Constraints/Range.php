<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Range extends Assert\Range
{
    public $notInRangeMessage = 'validation.rangeNotIn';
    public $minMessage = 'validation.rangeMin';
    public $maxMessage = 'validation.rangeMax';
    public $invalidMessage = 'validation.rangeNumber';
    public $invalidDateTimeMessage = 'validation.rangeDateTime';

    public function validatedBy(): string
    {
        return Assert\RangeValidator::class;
    }
}

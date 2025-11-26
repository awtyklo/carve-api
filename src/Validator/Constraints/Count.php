<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Count extends Assert\Count
{
    public $minMessage = 'validation.countMin';
    public $maxMessage = 'validation.countMax';
    public $exactMessage = 'validation.countExact';
    public $divisibleByMessage = 'validation.countDivisibleBy';

    public function validatedBy(): string
    {
        return Assert\CountValidator::class;
    }
}

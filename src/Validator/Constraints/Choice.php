<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Choice extends Assert\Choice
{
    public string $message = 'validation.choice';
    public string $multipleMessage = 'validation.choiceMultiple';
    public string $minMessage = 'validation.choiceMin';
    public string $maxMessage = 'validation.choiceMax';

    public function validatedBy(): string
    {
        return Assert\ChoiceValidator::class;
    }
}

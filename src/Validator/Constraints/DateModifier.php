<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class DateModifier extends Constraint
{
    public $message = 'validation.dateModifier';

    #[HasNamedArguments]
    public function __construct(string $message = null, array $groups = null, $payload = null)
    {
        $options = array_filter([
            'message' => $message ?? $this->message,
        ]);

        parent::__construct($options, $groups, $payload);
    }
}

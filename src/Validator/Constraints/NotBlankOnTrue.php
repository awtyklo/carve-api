<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlankOnTrue extends NotBlank
{
    public string $message = 'validation.required';

    #[HasNamedArguments]
    public function __construct(
        public string $propertyPath,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(
            groups: $groups,
            payload: $payload
        );

        $this->message = $message ?? $this->message;
    }
}

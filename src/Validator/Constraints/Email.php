<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Email extends Assert\Email
{
    public function __construct(
        ?array $options = null,
        ?string $message = 'validation.email',
        ?string $mode = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct(
            $options,
            $message,
            $mode,
            $normalizer,
            $groups,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\EmailValidator::class;
    }
}

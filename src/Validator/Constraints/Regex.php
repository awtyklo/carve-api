<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Regex extends Assert\Regex
{
    public function __construct(
        string|array|null $pattern,
        ?string $message = 'validation.regex',
        ?string $htmlPattern = null,
        ?bool $match = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct(
            $pattern,
            $message,
            $htmlPattern,
            $match,
            $normalizer,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return Assert\RegexValidator::class;
    }
}

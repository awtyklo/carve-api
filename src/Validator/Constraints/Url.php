<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Url extends Assert\Url
{
    public function __construct(
        ?array $options = null,
        ?string $message = 'validation.url',
        ?array $protocols = null,
        ?bool $relativeProtocol = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct(
            $options,
            $message,
            $protocols,
            $relativeProtocol,
            $normalizer,
            $groups,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\UrlValidator::class;
    }
}

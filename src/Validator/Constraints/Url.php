<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Url extends Assert\Url
{
    public string $message = 'validation.url';
    public string $tldMessage = 'validation.url';

    #[HasNamedArguments]
    public function __construct(
        ?string $message = null,
        array|string|null $protocols = null,
        ?bool $relativeProtocol = null,
        ?callable $normalizer = null,
        ?bool $requireTld = null,
        ?string $tldMessage = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(
            message: $message,
            protocols: $protocols,
            relativeProtocol: $relativeProtocol,
            normalizer: $normalizer,
            // Default to false. Not setting the requireTld option is deprecated since Symfony 7.1 and will default to true in Symfony 8.0.
            requireTld: $requireTld ?? false,
            tldMessage: $tldMessage,
            groups: $groups,
            payload: $payload
        );
    }

    public function validatedBy(): string
    {
        return Assert\UrlValidator::class;
    }
}

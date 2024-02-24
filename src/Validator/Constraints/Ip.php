<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Ip extends Assert\Ip
{
    public function __construct(
        ?array $options = null,
        ?string $version = null,
        ?string $message = 'validation.ip',
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct(
            $options,
            $version,
            $message,
            $normalizer,
            $groups,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return Assert\IpValidator::class;
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class OrmStringType extends Length
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        $exactly = null,
        int $min = null,
        int $max = 255,
        string $charset = null,
        callable $normalizer = null,
        string $exactMessage = null,
        string $minMessage = null,
        string $maxMessage = null,
        string $charsetMessage = null,
        array $groups = null,
        $payload = null,
        array $options = []
    ) {
        parent::__construct($exactly, $min, $max, $charset, $normalizer, $exactMessage, $minMessage, $maxMessage, $charsetMessage, $groups, $payload, $options);
    }

    public function validatedBy(): string
    {
        return Assert\LengthValidator::class;
    }
}

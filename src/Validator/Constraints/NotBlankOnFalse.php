<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlankOnFalse extends NotBlank
{
    public $propertyPath;

    #[HasNamedArguments]
    public function __construct(array $options = null, string $propertyPath = null, string $message = 'validation.required', bool $allowNull = null, callable $normalizer = null, array $groups = null, $payload = null)
    {
        $options = array_filter([
            'propertyPath' => $propertyPath ?? $this->propertyPath,
        ]);

        parent::__construct($options, $message, $allowNull, $normalizer, $groups, $payload);
    }

    public function getRequiredOptions(): array
    {
        return [
            'propertyPath',
        ];
    }
}

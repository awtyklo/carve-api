<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlankOnTrue extends NotBlank
{
    public $message = 'validation.required';
    public $propertyPath;

    #[HasNamedArguments]
    public function __construct(array $options = null, string $propertyPath = null, string $message = null, bool $allowNull = null, callable $normalizer = null, array $groups = null, $payload = null)
    {
        $options = array_filter([
            'propertyPath' => $propertyPath ?? $this->propertyPath,
        ]);

        parent::__construct($options, $message, $allowNull, $normalizer, $groups, $payload);
    }

    public function getRequiredOptions()
    {
        return [
            'propertyPath',
        ];
    }
}

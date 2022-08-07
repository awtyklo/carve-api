<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IdenticalCompare extends Constraint
{
    public $message = 'validation.identicalCompare';
    public $propertyPath1;
    public $propertyPath2;

    #[HasNamedArguments]
    public function __construct(string $propertyPath1 = null, string $propertyPath2 = null, string $message = null, array $groups = null, $payload = null)
    {
        $options = array_filter([
            'message' => $message ?? $this->message,
            'propertyPath1' => $propertyPath1 ?? $this->propertyPath1,
            'propertyPath2' => $propertyPath2 ?? $this->propertyPath2,
        ]);

        parent::__construct($options, $groups, $payload);
    }

    public function getRequiredOptions()
    {
        return [
            'propertyPath1',
            'propertyPath2',
        ];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

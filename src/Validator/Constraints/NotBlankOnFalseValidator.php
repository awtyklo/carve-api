<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class NotBlankOnFalseValidator extends NotBlankValidator
{
    private $propertyAccessor;

    public function validate($value, Constraint $constraint)
    {
        $path = $constraint->propertyPath;
        $object = $this->context->getObject();

        if (null === $object) {
            return;
        }

        try {
            $propertyPathValue = $this->getPropertyAccessor()->getValue($object, $path);
        } catch (NoSuchPropertyException $e) {
            throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: ', $path, get_debug_type($constraint)).$e->getMessage(), 0, $e);
        }

        if ($propertyPathValue) {
            return;
        }

        parent::validate($value, $constraint);
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}

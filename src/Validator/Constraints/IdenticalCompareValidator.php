<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IdenticalCompareValidator extends ConstraintValidator
{
    private $propertyAccessor;

    public function validate($protocol, Constraint $constraint): void
    {
        $value1 = $this->getPropertyAccessor()->getValue($protocol, $constraint->propertyPath1);
        $value2 = $this->getPropertyAccessor()->getValue($protocol, $constraint->propertyPath2);

        if ($value1 != $value2) {
            $this->context->buildViolation($constraint->message)->atPath($constraint->propertyPath1)->addViolation();
            $this->context->buildViolation($constraint->message)->atPath($constraint->propertyPath2)->addViolation();
        }
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}

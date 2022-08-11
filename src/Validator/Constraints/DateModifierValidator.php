<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * For full description of modifier format visit:
 * http://php.net/manual/en/datetime.modify.php.
 */
class DateModifierValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        try {
            $dateTime = new \DateTime();
            $result = $dateTime->modify($value);

            if (false === $result) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

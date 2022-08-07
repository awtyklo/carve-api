<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

#[\Attribute(\Attribute::TARGET_CLASS)]
class PasswordIdenticalCompare extends IdenticalCompare
{
    public $message = 'validation.passwordIdenticalCompare';

    public function validatedBy()
    {
        return IdenticalCompareValidator::class;
    }
}

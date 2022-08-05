<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class OrmIntegerType extends Compound
{
    protected function getConstraints(array $options): array
    {
        // Based on: https://dev.mysql.com/doc/refman/8.0/en/integer-types.html
        return [
            new GreaterThanOrEqual(-2147483648),
            new LessThanOrEqual(2147483647),
        ];
    }
}

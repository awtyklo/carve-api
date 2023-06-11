<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;

/**
 * Parameter with description that supports subject parameters.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Parameter extends OA\Parameter
{
}

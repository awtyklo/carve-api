<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response400 extends OA\Response
{
    public function __construct(
    ) {
        parent::__construct(response: 400, description: 'Unable to process request due to invalid data');
    }
}

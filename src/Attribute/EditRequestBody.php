<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class EditRequestBody extends OA\RequestBody
{
    public function __construct(
    ) {
        parent::__construct(content: new OA\JsonContent(type: 'object'));
    }
}

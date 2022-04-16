<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class EditIdParameter extends OA\Parameter
{
    public function __construct(
    ) {
        parent::__construct(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'), description: 'The ID of object to edit');
    }
}

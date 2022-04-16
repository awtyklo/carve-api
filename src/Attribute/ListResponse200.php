<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use Nelmio\ApiDocBundle\Annotation as NA;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ListResponse200 extends OA\Response
{
    public function __construct(
    ) {
        parent::__construct(response: 200, description: 'Returns list of objects', content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: 'rowsCount',
                        type: 'integer',
                        description: 'Number of total results',
                        example: 1,
                    ),
                    new OA\Property(
                        property: 'results',
                        type: 'array',
                        description: 'Paginated results',
                        items: new OA\Items(ref: new NA\Model()),
                    ),
                ],
            )
        ));
    }
}

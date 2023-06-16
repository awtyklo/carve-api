<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use Carve\ApiBundle\Model\BatchResult;
use Nelmio\ApiDocBundle\Annotation as NA;
use OpenApi\Attributes as OA;

/**
 * Preconfigured list response with code 200 and description that supports subject parameters and sets content as array of `Carve\ApiBundle\Model\BatchResult`.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response200BatchResults extends OA\Response
{
    public function __construct(string $description = 'Returns operation results')
    {
        // I do not know how to adjust this to similar idea as Response200, Response200Groups or Response200SubjectGroups
        parent::__construct(response: 200, description: $description, content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                type: 'array',
                items: new OA\Items(ref: new NA\Model(type: BatchResult::class))
            )
        ));
    }
}

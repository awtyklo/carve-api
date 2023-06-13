<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\XmlContent;

/**
 * Preconfigured path ID parameter with description that supports subject parameters and sets content as `Nelmio\ApiDocBundle\Annotation\Model` with subject class and serialization groups.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response200SubjectGroups extends OA\Response
{
    public function __construct(
        // $description is moved as first parameter to act as default value
        ?string $description = null,
        string|object|null $ref = null,
        int|string $response = 200,
        ?array $headers = null,
        MediaType|JsonContent|XmlContent|Attachable|array|null $content = null,
        ?array $links = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct(
            $ref,
            $response,
            $description,
            $headers,
            $content,
            $links,
            $x,
            $attachables,
        );
    }
}
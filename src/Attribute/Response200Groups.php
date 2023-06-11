<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\XmlContent;

/**
 * Preconfigured path ID parameter with description that supports subject parameters and attaches serialization groups to content.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response200Groups extends OA\Response
{
    public function __construct(
        string|object|null $ref = null,
        int|string $response = 200,
        ?string $description = null,
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

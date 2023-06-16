<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\XmlContent;

/**
 * Request body with description that supports subject parameters. When there is no content (`Nelmio\ApiDocBundle\Annotation\Model` is expected) it set as Api\Resource->batchFormClass. It also attaches 'sorting_field_choices' to content options.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RequestBodyBatch extends OA\RequestBody
{
    public function __construct(
        // $description is moved as first parameter to act as default value
        ?string $description = null,
        string|object|null $ref = null,
        ?string $request = null,
        ?bool $required = null,
        array|MediaType|JsonContent|XmlContent|Attachable|null $content = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct(
            $ref,
            $request,
            $description,
            $required,
            $content,
            $x,
            $attachables,
        );
    }
}

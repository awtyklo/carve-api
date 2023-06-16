<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\XmlContent;

/**
 * Request body with content set as Api\Resource->exportCsvFormClass (with 'sorting_field_choices', 'filter_filterBy_choices' and 'fields_field_choices' options) and description that supports subject parameters.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RequestBodyExportCsv extends OA\RequestBody
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

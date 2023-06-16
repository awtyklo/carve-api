<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\XmlContent;
use OpenApi\Generator;

/**
 * Preconfigured path ID parameter with description that supports subject parameters.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ParameterPathId extends OA\Parameter
{
    public function __construct(
        // $description is moved as first parameter to act as default value
        ?string $description = null,
        ?string $parameter = null,
        ?string $name = 'id',
        ?string $in = 'path',
        ?bool $required = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        string|object|null $ref = null,
        ?Schema $schema = new Schema(type: 'integer'),
        mixed $example = Generator::UNDEFINED,
        ?array $examples = null,
        array|JsonContent|XmlContent|Attachable|null $content = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?array $spaceDelimited = null,
        ?array $pipeDelimited = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct(
            $parameter,
            $name,
            $description,
            $in,
            $required,
            $deprecated,
            $allowEmptyValue,
            $ref,
            $schema,
            $example,
            $examples,
            $content,
            $style,
            $explode,
            $allowReserved,
            $spaceDelimited,
            $pipeDelimited,
            $x,
            $attachables,
        );
    }
}

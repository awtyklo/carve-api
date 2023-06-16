<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

use Nelmio\ApiDocBundle\Annotation as NA;
use OpenApi\Attributes as OA;
use OpenApi\Generator;

/**
 * Preconfigured response with code 200 and default description that supports subject parameters and sets content as array of `Nelmio\ApiDocBundle\Annotation\Model` with given class and serialization groups.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response200ArraySubjectGroups extends OA\Response
{
    private $class = Generator::UNDEFINED;

    public function __construct(string $class, ?string $description = 'Returns array of options')
    {
        $this->class = $class;

        // I do not know how to adjust this to similar idea as Response200, Response200Groups or Response200SubjectGroups
        parent::__construct(response: 200, description: $description, content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                type: 'array',
                items: new OA\Items(ref: new NA\Model()),
            )
        ));
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }
}

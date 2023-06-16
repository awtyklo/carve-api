<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Deny\AbstractApiObjectDeny;
use FOS\RestBundle\Controller\Annotations as Rest;

trait ApiGetTrait
{
    #[Rest\Get('/{id}', requirements: ['id' => '\d+'])]
    #[Api\Summary('Get {{ subjectLower }} by ID')]
    #[Api\ParameterPathId('ID of {{ subjectLower }} to return')]
    #[Api\Response200SubjectGroups]
    #[Api\Response404Id]
    public function getAction(int $id)
    {
        $object = $this->find($id, AbstractApiObjectDeny::GET);

        $this->modifyResponseObject($object);

        return $object;
    }
}

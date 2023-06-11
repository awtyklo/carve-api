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
    #[Api\GetIdParameter]
    #[Api\GetResponse200]
    #[Api\Response404]
    public function getAction(int $id)
    {
        $object = $this->find($id, AbstractApiObjectDeny::GET);

        $this->modifyResponseObject($object);

        return $object;
    }
}

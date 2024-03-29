<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Deny\AbstractApiObjectDeny;
use FOS\RestBundle\Controller\Annotations as Rest;

trait ApiDeleteTrait
{
    #[Rest\Delete('/{id}', requirements: ['id' => '\d+'])]
    #[Api\Summary('Delete {{ subjectLower }} by ID')]
    #[Api\ParameterPathId('ID of {{ subjectLower }} to delete')]
    #[Api\Response204Delete]
    #[Api\Response404Id]
    public function deleteAction(int $id)
    {
        $object = $this->find($id, AbstractApiObjectDeny::DELETE);

        return $this->processDelete($object);
    }

    protected function processDelete(object $object)
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }
}

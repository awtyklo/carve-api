<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use Carve\ApiBundle\Deny\AbstractApiObjectDeny;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiEditTrait
{
    #[Rest\Post('/{id}', requirements: ['id' => '\d+'])]
    #[Api\Summary('Edit {{ subjectLower }} by ID')]
    #[Api\ParameterPathId('ID of {{ subjectLower }} to edit')]
    #[Api\EditRequestBody]
    #[Api\Response200SubjectGroups('Returns edited {{ subjectLower }}')]
    #[Api\Response400]
    #[Api\Response404]
    public function editAction(Request $request, int $id)
    {
        $object = $this->find($id, AbstractApiObjectDeny::EDIT);

        return $this->handleForm($this->getEditFormClass(), $request, [$this, 'processEdit'], $object, $this->getEditFormOptions());
    }

    protected function getEditFormOptions(): array
    {
        return [];
    }

    protected function processEdit($object, FormInterface $form)
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->modifyResponseObject($object);
        
        return $object;
    }
}

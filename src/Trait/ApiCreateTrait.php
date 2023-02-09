<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiCreateTrait
{
    #[Rest\Post('/create')]
    #[Api\CreateDescription]
    #[Api\CreateRequestBody]
    #[Api\CreateResponse200]
    #[Api\Response400]
    public function createAction(Request $request)
    {
        return $this->handleForm($this->getCreateFormClass(), $request, [$this, 'processCreate'], $this->getCreateObject(), $this->getCreateFormOptions());
    }

    protected function getCreateObject()
    {
        return null;
    }

    protected function getCreateFormOptions(): array
    {
        return [];
    }

    protected function processCreate($object, FormInterface $form)
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->modifyResponseObject($object);

        return $object;
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Trait;

use Carve\ApiBundle\Attribute as Api;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ApiListTrait
{
    #[Rest\Post('/list')]
    #[Api\ListDescription]
    #[Api\ListRequestBody]
    #[Api\ListResponse200]
    #[Api\Response400]
    public function listAction(Request $request)
    {
        return $this->handleForm($this->getListFormClass(), $request, [$this, 'processList'], $this->getListObject(), $this->getListFormOptions());
    }

    protected function getListObject()
    {
        return null;
    }

    protected function processListObject($object)
    {
    }

    protected function getListFormOptions(): array
    {
        return $this->getDefaultListFormOptions();
    }

    protected function processList($object, FormInterface $form)
    {
        $queryBuilder = $this->getListQueryBuilder($object);

        $rowCountQueryBuilder = clone $queryBuilder;
        $rowCountQueryBuilder->select('COUNT(DISTINCT o.id)');
        $rowCountQueryBuilder->resetDQLPart('orderBy');
        $rowCountQueryBuilder->setFirstResult(0);
        $rowCountQueryBuilder->setMaxResults(1);
        $rowCount = $rowCountQueryBuilder->getQuery()->getSingleScalarResult();

        $results = $queryBuilder->getQuery()->getResult();

        if ($this->hasDenyClass()) {
            $denyClass = $this->getDenyClass();
            foreach ($results as $result) {
                $this->fillDeny($denyClass, $result);
                $this->processListObject($result);
            }
        }

        return [
            'results' => $results,
            'rowCount' => (int) $rowCount,
        ];
    }
}

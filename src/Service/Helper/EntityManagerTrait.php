<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Service\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

trait EntityManagerTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getRepository($entity): EntityRepository
    {
        return $this->entityManager->getRepository($entity);
    }
}

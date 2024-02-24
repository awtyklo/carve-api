<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Service\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\Service\Attribute\Required;

trait EntityManagerTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getRepository($entity): EntityRepository
    {
        return $this->entityManager->getRepository($entity);
    }
}

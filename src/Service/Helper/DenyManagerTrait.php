<?php

namespace Carve\ApiBundle\Service\Helper;

use Carve\ApiBundle\Deny\DenyInterface;
use Carve\ApiBundle\Service\DenyManager;
use Symfony\Contracts\Service\Attribute\Required;

trait DenyManagerTrait
{
    protected DenyManager $denyManager;

    #[Required]
    public function setDenyManager(DenyManager $denyManager)
    {
        $this->denyManager = $denyManager;
    }

    public function isDenied(string $denyClass, string $denyKey, DenyInterface $object): bool
    {
        return $this->denyManager->isDenied($denyClass, $denyKey, $object);
    }

    public function fillDeny(string $denyClass, DenyInterface $object): void
    {
        $this->denyManager->fillDeny($denyClass, $object);
    }
}

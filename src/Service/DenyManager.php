<?php

namespace Carve\ApiBundle\Service;

use Carve\ApiBundle\Deny\AbstractApiObjectDeny;
use Carve\ApiBundle\Deny\DenyInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DenyManager
{
    protected $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function isDenied(string $denyClass, string $denyKey, DenyInterface $object): bool
    {
        return $this->getDenyObject($denyClass)->isDenied($denyKey, $object);
    }

    public function fillDeny(string $denyClass, DenyInterface $object): void
    {
        $this->getDenyObject($denyClass)->fillDeny($object);
    }

    protected function getDenyObject(string $denyClass): AbstractApiObjectDeny
    {
        if (!$this->locator->has($denyClass)) {
            throw new \Exception('Class "'.$denyClass.'" is not available in service locator that include services tagged with "carve_api.object_deny"');
        }

        return $this->locator->get($denyClass);
    }
}

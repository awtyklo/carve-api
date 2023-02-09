<?php

namespace Carve\ApiBundle\Service;

use Carve\ApiBundle\Attribute\AddRoleBasedSerializerGroups;
use ReflectionClass;
use Symfony\Component\Security\Core\Security;

class RoleBasedSerializerGroupsManager
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getRoleBasedSerializerGroupsByOwner(array $viewOwner): array
    {
        if (!isset($viewOwner[0])) {
            throw new \Exception('Owner not provided in view');
        }

        if (\is_string($viewOwner[0])) {
            $class = $viewOwner[0];
        } elseif (\is_object($viewOwner[0])) {
            $class = \get_class($viewOwner[0]);
        } else {
            throw new \Exception('Owner not provided in view');
        }

        return $this->getRoleBasedSerializerGroups($class);
    }

    public function getRoleBasedSerializerGroups(string $controllerClass): array
    {
        $reflection = new ReflectionClass($controllerClass);

        $attributes = $reflection->getAttributes(AddRoleBasedSerializerGroups::class);

        $serializerGroups = [];
        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance->getAttribute() && $this->security->isGranted($attributeInstance->getAttribute())) {
                $serializerGroups = array_unique(array_merge($serializerGroups, $attributeInstance->getSerializerGroups()));
            }
        }

        return $serializerGroups;
    }
}

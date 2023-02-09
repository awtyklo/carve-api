<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Service\Helper;

use Carve\ApiBundle\Service\RoleBasedSerializerGroupsManager;
use Symfony\Contracts\Service\Attribute\Required;

trait RoleBasedSerializerGroupsManagerTrait
{
    protected RoleBasedSerializerGroupsManager $roleBasedSerializerGroupsManager;

    #[Required]
    public function setRoleBasedSerializerGroupsManager(RoleBasedSerializerGroupsManager $roleBasedSerializerGroupsManager)
    {
        $this->roleBasedSerializerGroupsManager = $roleBasedSerializerGroupsManager;
    }

    public function getRoleBasedSerializerGroups(string $controllerClass): array
    {
        return $this->roleBasedSerializerGroupsManager->getRoleBasedSerializerGroups($controllerClass);
    }

    public function getRoleBasedSerializerGroupsByOwner(array $viewOwner): array
    {
        return $this->roleBasedSerializerGroupsManager->getRoleBasedSerializerGroupsByOwner($viewOwner);
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

/**
 * This extends serialization groups for sorting, filtering and export as well.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class AddRoleBasedSerializerGroups
{
    // User Role named as per symfony naming convention
    public string $attribute;
    public array $serializerGroups = [];

    public function __construct(
        string $attribute,
        array $serializerGroups = []
    ) {
        $this->attribute = $attribute;
        $this->serializerGroups = $serializerGroups;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function setAttribute(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getSerializerGroups(): array
    {
        return $this->serializerGroups;
    }

    public function setSerializerGroups(array $serializerGroups)
    {
        $this->serializerGroups = $serializerGroups;
    }
}

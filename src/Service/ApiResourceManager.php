<?php

namespace Carve\ApiBundle\Service;

use Carve\ApiBundle\Attribute as Api;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Generator;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

class ApiResourceManager
{
    protected PropertyListExtractorInterface $serializerExtractor;

    public function __construct(PropertyListExtractorInterface $serializerExtractor)
    {
        $this->serializerExtractor = $serializerExtractor;
    }

    public function getFieldsFieldChoices(\ReflectionClass|\ReflectionMethod $reflection): array
    {
        $fieldSerializerGroups = $this->getAttributeArgument($reflection, 'exportFormFieldGroups');
        if (null === $fieldSerializerGroups) {
            $defaultSerializerGroups = $this->getSerializerGroups($reflection);

            if (null !== $defaultSerializerGroups) {
                // We do not need to apply getSerializableSerializerGroups here
                $fieldSerializerGroups = $defaultSerializerGroups;
            } else {
                // Fake group to avoid using empty serializer groups which leads to serializing everything
                $fieldSerializerGroups = ['__invalid__'];
            }
        }

        $class = $this->getAttributeArgument($reflection, 'class');
        $fieldsFieldChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $fieldSerializerGroups]);

        $fieldAppendChoices = $this->getAttributeArgument($reflection, 'exportFormFieldAppend');
        if (null !== $fieldAppendChoices) {
            $fieldsFieldChoices = self::appendChoices($fieldsFieldChoices, $fieldAppendChoices);
        }

        return $fieldsFieldChoices;
    }

    public function getFilterFilterByChoices(\ReflectionClass|\ReflectionMethod $reflection): array
    {
        $filterBySerializerGroups = $this->getAttributeArgument($reflection, 'listFormFilterByGroups');
        if (null === $filterBySerializerGroups) {
            $defaultSerializerGroups = $this->getSerializerGroups($reflection);

            if (null !== $defaultSerializerGroups) {
                $filterBySerializerGroups = self::getSerializableSerializerGroups($defaultSerializerGroups);
            } else {
                // Fake group to avoid using empty serializer groups which leads to serializing everything
                $filterBySerializerGroups = ['__invalid__'];
            }
        }

        $class = $this->getAttributeArgument($reflection, 'class');
        $filterByChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $filterBySerializerGroups]);

        $filterByAppend = $this->getAttributeArgument($reflection, 'listFormFilterByAppend');
        if (null !== $filterByAppend) {
            $filterByChoices = self::appendChoices($filterByChoices, $filterByAppend);
        }

        return $filterByChoices;
    }

    public function getSortingFieldChoices(\ReflectionClass|\ReflectionMethod $reflection): array
    {
        $sortingSerializerGroups = $this->getAttributeArgument($reflection, 'listFormSortingFieldGroups');
        if (null === $sortingSerializerGroups) {
            $defaultSerializerGroups = $this->getSerializerGroups($reflection);

            if (null !== $defaultSerializerGroups) {
                $sortingSerializerGroups = self::getSerializableSerializerGroups($defaultSerializerGroups);
            } else {
                // Fake group to avoid using empty serializer groups which leads to serializing everything
                $sortingSerializerGroups = ['__invalid__'];
            }
        }

        $class = $this->getAttributeArgument($reflection, 'class');
        $sortingFieldChoices = $this->serializerExtractor->getProperties($class, ['serializer_groups' => $sortingSerializerGroups]);

        $sortingFieldAppend = $this->getAttributeArgument($reflection, 'listFormSortingFieldAppend');
        if (null !== $sortingFieldAppend) {
            $sortingFieldChoices = self::appendChoices($sortingFieldChoices, $sortingFieldAppend);
        }

        return $sortingFieldChoices;
    }

    public function getSerializerGroups(\ReflectionClass|\ReflectionMethod $reflection): ?array
    {
        if ($reflection instanceof \ReflectionMethod) {
            foreach ($reflection->getAttributes(Rest\View::class) as $attribute) {
                $attributeInstance = $attribute->newInstance();
                $serializerGroups = $attributeInstance->getSerializerGroups();

                return count($serializerGroups) > 0 ? $serializerGroups : null;
            }

            $reflection = $reflection->getDeclaringClass();
        }

        foreach ($reflection->getAttributes(Rest\View::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $serializerGroups = $attributeInstance->getSerializerGroups();

            return count($serializerGroups) > 0 ? $serializerGroups : null;
        }

        // Additionally check parent class
        $reflectionParent = $reflection->getParentClass();
        foreach ($reflectionParent->getAttributes(Rest\View::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();
            $serializerGroups = $attributeInstance->getSerializerGroups();

            return count($serializerGroups) > 0 ? $serializerGroups : null;
        }

        return null;
    }

    /**
     * Return $argument (i.e. "class", "createFormClass", "listFormSortingFieldGroups") for reflection class or method.
     * When Api\Resource is not found in $reflection an exception is thrown.
     * When Api\Resource->$argument is not set or is set to OpenApi\Generator::UNDEFINED null value is returned.
     */
    public function getAttributeArgument(\ReflectionClass|\ReflectionMethod $reflection, string $argument): mixed
    {
        if ($reflection instanceof \ReflectionMethod) {
            $reflection = $reflection->getDeclaringClass();
        }

        foreach ($reflection->getAttributes(Api\Resource::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            if (!isset($attributeInstance->$argument)) {
                return null;
            }

            if ($attributeInstance->$argument === Generator::UNDEFINED) {
                return null;
            }

            return $attributeInstance->$argument;
        }

        throw new \Exception('Could not find "Carve\ApiBundle\Attribute\Resource" attribute is in '.$reflection->getName());
    }

    /**
     * Check whether reflection class or method has Api\Resource->$argument (i.e. "class", "createFormClass", "listFormSortingFieldGroups") for reflection class or method.
     * When Api\Resource is not found it returns false.
     * When Api\Resource->$argument is not set or is set to OpenApi\Generator::UNDEFINED false is returned.
     */
    public function hasAttributeArgument(\ReflectionClass|\ReflectionMethod $reflection, string $argument): bool
    {
        if ($reflection instanceof \ReflectionMethod) {
            $reflection = $reflection->getDeclaringClass();
        }

        foreach ($reflection->getAttributes(Api\Resource::class) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            if (!isset($attributeInstance->$argument)) {
                return false;
            }

            if ($attributeInstance->$argument === Generator::UNDEFINED) {
                return false;
            }

            return true;
        }

        return false;
    }

    public static function getSerializableSerializerGroups(array $serializerGroups): array
    {
        // 'identification' is a helper serialization group that includes 'id' and 'representation'

        // Replace serializer group 'identification' with 'id' ('identification' is not serializable)
        $identificationKey = array_search('identification', $serializerGroups);
        if (false !== $identificationKey) {
            array_splice($serializerGroups, $identificationKey, 1, ['id']);
        }

        // Remove serializer group 'representation' which is not serializable
        $representationKey = array_search('representation', $serializerGroups);
        if (false !== $representationKey) {
            array_splice($serializerGroups, $representationKey, 1);
        }

        // Remove serializer group 'deny' which is not serializable
        $denyKey = array_search('deny', $serializerGroups);
        if (false !== $denyKey) {
            array_splice($serializerGroups, $denyKey, 1);
        }

        return $serializerGroups;
    }

    public static function appendChoices(array $choices, array $appendChoices): array
    {
        return array_unique(array_merge($choices, $appendChoices));
    }
}

<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer\Normalizer;

use Carve\ApiBundle\Attribute\Export\ExportEnumPrefix;
use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Doctrine\ORM\Mapping\ReflectionEnumProperty;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\Persistence\Proxy;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportEnumNormalizer implements ContextAwareNormalizerInterface
{
    public const EXPORT_GROUP = 'special:export';

    private $normalizer;
    private $registry;
    private $translator;
    private $propertyAccessor;

    private $cache = [];

    public function __construct(ObjectNormalizer $normalizer, ManagerRegistry $registry, TranslatorInterface $translator)
    {
        $this->normalizer = $normalizer;
        $this->registry = $registry;
        $this->translator = $translator;
    }

    public function normalize($object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        $class = get_class($object);
        foreach ($this->getEnumFields($class) as $enumField) {
            $enum = $this->getPropertyAccessor()->getValue($object, $enumField);
            if ($enum) {
                $data[$enumField] = $this->translator->trans($this->getExportEnumPrefix($class, $enumField).$enum->value);
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (!in_array(self::EXPORT_GROUP, $context['groups'] ?? [])) {
            return false;
        }

        if (!is_object($data)) {
            return false;
        }

        $enumFields = $this->getEnumFields(get_class($data));

        return count($enumFields) > 0;
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    protected function getEnumFields(string $class): array
    {
        $ret = $this->getMetadata($class);
        if (!$ret) {
            return [];
        }

        [$metadata] = $ret;

        $enumFields = [];
        foreach ($metadata->getFieldNames() as $fieldName) {
            $reflectionProperty = $metadata->getReflectionProperty($fieldName);
            if ($reflectionProperty instanceof ReflectionEnumProperty) {
                $enumFields[] = $fieldName;
            }
        }

        return $enumFields;
    }

    protected function getExportEnumPrefix(string $class, string $property): string
    {
        $reflectionProperty = new ReflectionProperty($class, $property);
        $attributes = $reflectionProperty->getAttributes(ExportEnumPrefix::class);

        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();

            return $attributeInstance->getPrefix();
        }

        $reflectionClass = new ReflectionClass($class);

        return 'enum.'.strtolower($reflectionClass->getShortName()).'.'.$property.'.';
    }

    // Code below from Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser

    protected function getMetadata(string $class)
    {
        // normalize class name
        $class = self::getRealClass(ltrim($class, '\\'));

        if (\array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $this->cache[$class] = null;
        foreach ($this->registry->getManagers() as $name => $em) {
            try {
                return $this->cache[$class] = [$em->getClassMetadata($class), $name];
            } catch (MappingException $e) {
                // not an entity or mapped super class
            } catch (LegacyMappingException $e) {
                // not an entity or mapped super class, using Doctrine ORM 2.2
            }
        }

        return null;
    }

    private static function getRealClass(string $class): string
    {
        if (false === $pos = strrpos($class, '\\'.Proxy::MARKER.'\\')) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}

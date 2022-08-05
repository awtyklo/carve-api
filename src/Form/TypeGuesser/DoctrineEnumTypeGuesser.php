<?php

namespace Carve\ApiBundle\Form\TypeGuesser;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Doctrine\ORM\Mapping\ReflectionEnumProperty;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

class DoctrineEnumTypeGuesser implements FormTypeGuesserInterface
{
    protected $registry;

    private $cache = [];

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function guessType($class, $property): ?TypeGuess
    {
        $ret = $this->getMetadata($class);
        if (!$ret) {
            return null;
        }

        [$metadata] = $ret;

        $reflectionProperty = $metadata->getReflectionProperty($property);
        if ($reflectionProperty instanceof ReflectionEnumProperty) {
            $enumType = null;

            $attributes = $reflectionProperty->getAttributes(Column::class);
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                $enumType = $attributeInstance->enumType;
                break;
            }

            if (null === $enumType) {
                return null;
            }

            return new TypeGuess(EnumType::class, ['class' => $enumType, 'invalid_message' => 'validation.enum'], Guess::VERY_HIGH_CONFIDENCE);
        }

        return null;
    }

    public function guessRequired(string $class, string $property): ?ValueGuess
    {
        return null;
    }

    public function guessMaxLength(string $class, string $property): ?ValueGuess
    {
        return null;
    }

    public function guessPattern(string $class, string $property): ?ValueGuess
    {
        return null;
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

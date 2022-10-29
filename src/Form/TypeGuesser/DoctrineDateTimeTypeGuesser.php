<?php

namespace Carve\ApiBundle\Form\TypeGuesser;

use Carve\ApiBundle\Form\Type\DateTimeType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

class DoctrineDateTimeTypeGuesser implements FormTypeGuesserInterface
{
    protected $registry;

    private $cache = [];

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function guessType($class, $property): ?TypeGuess
    {
        if (!$ret = $this->getMetadata($class)) {
            return null;
        }

        [$metadata] = $ret;

        if ($metadata->hasAssociation($property)) {
            return null;
        }

        switch ($metadata->getTypeOfField($property)) {
            case Types::DATETIME_MUTABLE:
            case Types::DATETIMETZ_MUTABLE:
                return new TypeGuess(DateTimeType::class, [], Guess::VERY_HIGH_CONFIDENCE);
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

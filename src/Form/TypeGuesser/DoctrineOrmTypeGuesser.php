<?php

namespace Carve\ApiBundle\Form\TypeGuesser;

use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

class DoctrineOrmTypeGuesser implements FormTypeGuesserInterface
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

        list($metadata, $name) = $ret;

        if ($metadata->hasAssociation($property)) {
            return null;
        }

        switch ($metadata->getTypeOfField($property)) {
            case Type::DATETIME:
            case Type::DATETIMETZ:
            case 'vardatetime':
                return new TypeGuess(DateTimeType::class, [], Guess::HIGH_CONFIDENCE);
            case 'datetime_immutable':
            case 'datetimetz_immutable':
                return new TypeGuess(DateTimeType::class, ['input' => 'datetime_immutable'], Guess::HIGH_CONFIDENCE);
            case Type::DATE:
                return new TypeGuess(DateType::class, [], Guess::HIGH_CONFIDENCE);
            case 'date_immutable':
                return new TypeGuess(DateType::class, ['input' => 'datetime_immutable'], Guess::HIGH_CONFIDENCE);
            case Type::TIME:
                return new TypeGuess(TimeType::class, [], Guess::HIGH_CONFIDENCE);
            case 'time_immutable':
                return new TypeGuess(TimeType::class, ['input' => 'datetime_immutable'], Guess::HIGH_CONFIDENCE);
            case Type::INTEGER:
            case Type::BIGINT:
            case Type::SMALLINT:
                return new TypeGuess(IntegerType::class, [], Guess::MEDIUM_CONFIDENCE);
        }
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
